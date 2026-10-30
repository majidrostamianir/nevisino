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

    public function updatingSearchQuery(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $visits = ($this->searchQuery
            ? Visit::search($this->searchQuery)
            : Visit::query()->latest())
            ->paginate(500);

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

        $cleanUrl    = $this->stripDomain($item->url);
        $currentHref = ($isGhesti ? 'https://ghesti.nevisino.ir' : 'https://nevisino.ir') . $cleanUrl;

        $refIsExternal = $this->isExternalReferrer($item->referrer);
        $refColor      = $refIsExternal ? 'text-gray-400' : $linkColor;
        $cleanRef      = $this->stripDomain($item->referrer);

        $user = $item->user_id ? User::find($item->user_id) : null;

        $userName    = null;
        $userHref    = null;
        $userClass   = null;
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

            'link_color'  => $linkColor,
            'clean_url'   => $cleanUrl,
            'current_href'=> $currentHref,

            'has_ref'     => (bool) $item->referrer,
            'ref_href'    => $item->referrer,
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

    /** حذف پیشوند پروتکل، www، ghesti و nevisino.ir از URL برای نمایش کوتاه. */
    protected function stripDomain(?string $url): string
    {
        if (!$url) {
            return '';
        }

        $path = preg_replace('#^https?://(www\.)?#', '', $url);
        $path = preg_replace('#^(ghesti\.)?nevisino\.ir#', '', $path);

        return $path === '' ? '/' : $path;
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