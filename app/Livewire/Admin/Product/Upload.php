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
    public array $picture = [];
    public $uploadProgress = 101;
    public int $imageVersion = 1;
    public string $folder;


    protected $listeners = ['imageUpdated' => 'refreshImage'];

    public function refreshImage()
    {
        $this->imageVersion = now()->timestamp;
    }

    public function updatedPicture(): void
    {
        $this->upload();
        $this->dispatch('imageUpdated');

    }

    public function upload(): void
    {
        $this->validate([
            'picture' => 'required|array',
            'picture.*' => 'required|image|max:10240',
        ]);
        $manager = new ImageManager(new Driver());

//        $watermark = $manager->read(public_path('images/watermark.png'));
//        $picture->place($watermark);

        $sizes = [
            'large' => 1050,
            'small' => 350,
        ];
        $sortedPictures = collect($this->picture)
            ->sortBy(function ($file) {
                return $file->getClientOriginalName();
            }, SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        foreach ($sortedPictures as $key => $image) {
            $picture = $manager->read($image);
            $folder = 'products/' . $this->product->id;
            $index = $key + 1;

            foreach ($sizes as $name => $height) {
                $path = "$folder/$name";

                Storage::disk('public')->makeDirectory($path);

                $picture->scale(height: $height)->toWebp()
                    ->save(Storage::disk('public')->path("$path/{$index}.webp"));
            }

        }


        $this->dispatch('imageUpdated');

    }

    public function render()
    {
        return view('livewire.admin.product.upload');
    }
}
