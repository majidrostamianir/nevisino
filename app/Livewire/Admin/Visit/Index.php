<?php

namespace App\Livewire\Admin\Visit;

use App\Models\User;
use App\Models\Visit;
use App\Helpers\UserAgentParser;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $searchQuery = '';

    /** پیش‌فرض: ربات‌ها نمایش داده نشوند */
    public bool $showBots = false;

    public function updatingSearchQuery(): void
    {
        $this->resetPage();
    }

    public function updatingShowBots(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->searchQuery
            ? Visit::search($this->searchQuery)
            : Visit::query()->latest();

        // به‌صورت پیش‌فرض ربات‌ها فیلتر می‌شوند
        if (! $this->showBots) {
            $query->where('is_bot', false);
        }

        $visits = $query->paginate(500);

        $visits->getCollection()->transform(fn (Visit $item) => $this->formatVisit($item));

        return view('livewire.admin.visit.index', compact('visits'))
            ->layout('components.layouts.admin');
    }

    /**
     * همه‌ی منطق نمایش در همین‌جا محاسبه می‌شود تا ویو تمیز بماند.
     */
    protected function formatVisit(Visit $item): array
    {
        $isGhesti = (bool) $item->is_ghesti;

        $linkColor = $isGhesti ? 'text-purple-500 font-medium' : 'text-gray-700';

        // base دامنه بر اساس نوع بازدید
        $base = $isGhesti ? 'https://ghesti.nevisino.ir' : 'https://nevisino.ir';

        // مسیر تمیز و نرمال‌شده (همیشه با / شروع می‌شود)
        $cleanUrl    = $this->stripDomain($item->url);
        $currentHref = rtrim($base, '/') . '/' . ltrim($cleanUrl, '/');

        // ریفرر
        $refIsExternal = $this->isExternalReferrer($item->referrer);
        $refColor      = $refIsExternal ? 'text-gray-400' : $linkColor;
        $cleanRef      = $this->stripDomain($item->referrer);

        // href ریفرر: اگر کامل و معتبر بود همان، وگرنه بساز
        $refHref = $item->referrer
            ? (str_starts_with($item->referrer, 'http')
                ? $item->referrer
                : rtrim($base, '/') . '/' . ltrim($item->referrer, '/'))
            : null;

        $user = $item->user_id ? User::find($item->user_id) : null;

        $userName  = null;
        $userHref  = null;
        $userClass = null;
        if ($user) {
            $userName  = $user->name ?? english_to_persian_num($user->mobile) ?? '—';
            $userHref  = 'https://nevisino.ir/admin/user/order/' . $item->user_id;
            $userClass = $user->mobile_verified_at
                ? 'text-emerald-400 font-semibold'
                : 'text-gray-600';
        }

        $ua = UserAgentParser::parse($item->user_agent);

        return [
            'id'          => $item->id,
            'is_ghesti'   => $isGhesti,
            'is_bot'      => (bool) $item->is_bot,
            'ip'          => english_to_persian_num($item->ip),
            'created_at'  => english_to_persian_num(verta($item->created_at)->format('m/d#H:i:s')),

            'link_color'   => $linkColor,
            'clean_url'    => $cleanUrl,
            'current_href' => $currentHref,

            'has_ref'     => (bool) $item->referrer,
            'ref_href'    => $refHref ?? '#',
            'clean_ref'   => $cleanRef,
            'ref_color'   => $refColor,

            'user' => $user ? [
                'name'  => $userName,
                'href'  => $userHref,
                'class' => $userClass,
            ] : null,

            'ua' => [
                'icon'    => $ua['icon'],
                'browser' => $ua['browser'],
                'os'      => $ua['os'],
                'full'    => $ua['full'],
            ],
        ];
    }

    /**
     * حذف پیشوند پروتکل، www، ghesti و nevisino.ir از URL برای نمایش کوتاه.
     * خروجی همیشه با "/" (یا "?" یا "#") شروع می‌شود.
     */
    protected function stripDomain(?string $url): string
    {
        if (!$url) {
            return '/';
        }

        // حذف پروتکل و www
        $path = preg_replace('#^https?://(www\.)?#', '', $url);

        // حذف دامنه‌ی nevisino.ir یا ghesti.nevisino.ir در ابتدا
        $path = preg_replace('#^(ghesti\.)?nevisino\.ir#', '', $path);

        // اگر خالی شد
        if ($path === '' || $path === false) {
            return '/';
        }

        // اگر بدون / شروع شده، اضافه کن
        if (!str_starts_with($path, '/')
            && !str_starts_with($path, '?')
            && !str_starts_with($path, '#')) {
            $path = '/' . $path;
        }

        return $path;
    }

    /** آیا ریفرر به دامنه‌ای خارج از nevisino.ir اشاره می‌کند؟ */
    protected function isExternalReferrer(?string $referrer): bool
    {
        if (!$referrer) {
            return false;
        }

        $host = parse_url($referrer, PHP_URL_HOST) ?? '';

        return $host !== '' && !str_ends_with($host, 'nevisino.ir');
    }
}