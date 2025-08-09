<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Livewire\Component;
use Livewire\WithFileUploads;

class Upload extends Component
{
    use WithFileUploads;

    public Product $product;
    public $picture;
    public $uploadProgress = 101;
    public int $imageVersion =1;
    public string $folder;


    protected $listeners = ['imageUpdated' => 'refreshImage'];

    public function refreshImage()
    {
        $this->imageVersion = now()->timestamp;
    }
    public function updatedMockup(): void
    {
        $this->upload();
        $this->dispatch('imageUpdated');

    }

    public function upload(): void
    {
        $this->validate([
            'picture' => 'required|image|max:10240',
        ]);

        $manager = new ImageManager(new Driver());

        $mock_front = $manager->read($this->mockup)->scale(1200);
        $mock_watermark = $manager->read(\Storage::disk('private')->path('free/watermark.png'));
        $mock_front->place($mock_watermark);
        $mock_front->scale(height: 1050)->toWebp()->save(Storage::disk('private')->path('free/largeFront.webp'));
        $mock_front->scale(height: 525)->toWebp()->save(Storage::disk('private')->path('free/averageFront.webp'));
        $mock_front->scale(height: 350)->toWebp()->save(Storage::disk('private')->path('free/smallFront.webp'));

        $localLargePathFront = '/free/largeFront.webp';
        $localAveragePathFront = '/free/averageFront.webp';
        $localSmallPathFront = '/free/smallFront.webp';
        $serverLargePathFront = 'Pictures/'. $this->folder .'/Large/Front/' . $this->product->id . '.webp';
        $serverAveragePathFront = 'Pictures/'. $this->folder .'/Average/Front/' . $this->product->id . '.webp';
        $serverSmallPathFront = 'Pictures/'. $this->folder .'/Small/Front/' . $this->product->id . '.webp';
        $largeContentFront = Storage::disk('private')->get($localLargePathFront);
        $averageContentFront = Storage::disk('private')->get($localAveragePathFront);
        $smallContentFront = Storage::disk('private')->get($localSmallPathFront);

        Storage::disk('ftp')->put($serverLargePathFront, $largeContentFront);
        Storage::disk('ftp')->put($serverAveragePathFront, $averageContentFront);
        Storage::disk('ftp')->put($serverSmallPathFront, $smallContentFront);

        $this->product->update([
            'url' => 'https://dl.sungraphic.ir/' . $serverSmallPathFront,
            'width' => $mock_front->width()]);

        if (Storage::disk('ftp')->exists('mainFiles/'. $this->folder .'/' . $this->product->id . '.zip')){
            $this->product->update(['status' => true]);
        }
        $this->dispatch('imageUpdated');

    }

    public function render()
    {
        return view('livewire.admin.product.upload');
    }
}
