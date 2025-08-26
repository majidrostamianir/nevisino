<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use App\Models\ProductVariant;
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
    public int $count;


    public $regularImages = [];
    public $variantImages = [];
    public ProductVariant|null $variant = null;



    protected function loadImages()
    {
        $files = Storage::disk('public')->files('products/' . $this->product->id . '/large');

        $regular = [];
        $variant = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'webp') continue;

            $name = pathinfo($file, PATHINFO_FILENAME);

            if (is_numeric($name)) {
                if ((int)$name < 1000) {
                    $regular[] = $name;
                } else {
                    $variant[] = $name;
                }
            }
        }

        sort($regular);
        sort($variant);

        $this->regularImages = $regular;
        $this->variantImages = $variant;
    }
    protected $listeners = ['imageUpdated' => 'refreshImage'];

    public function mount()
    {
        $this->pictureCount();
        $this->loadImages();
    }

    public function refreshImage()
    {
        $this->imageVersion = now()->timestamp;
    }

    public function updatedPicture(): void
    {
        $this->upload();
        $this->dispatch('imageUpdated');
    }

    public function pictureCount()
    {
        $folder = 'products/' . $this->product->id . '/large';
        $files = Storage::disk('public')->files($folder);
        $this->count = count($files);
    }

    public function upload(): void
    {
        $this->validate([
            'picture' => 'required|array',
            'picture.*' => 'required|image|max:10240',
        ]);
        $manager = new ImageManager(new Driver());
        $folder = 'products/' . $this->product->id;
        $sizes = ['large' => 1400, 'small' => 525,];
        $sortedPictures = collect($this->picture)->sortBy(function ($file) {
            return $file->getClientOriginalName();
        }, SORT_NATURAL | SORT_FLAG_CASE)->values()->all();

//        $watermark = $manager->read(public_path('images/watermark.png'));
//        $picture->place($watermark);


        foreach ($sortedPictures as $key => $image) {
            $picture = $manager->read($image);

            if ($this->variant) {
                $index = $this->variant->id;
            } else {
                $index = $key + 1;
            }
            foreach ($sizes as $name => $height) {
                $path = "$folder/$name";

                Storage::disk('public')->makeDirectory($path);

                $picture->scale(height: $height)->toWebp()
                    ->save(Storage::disk('public')->path("$path/{$index}.webp"));
            }
        }
        $this->dispatch('imageUpdated');
        $this->pictureCount();
    }

    public function render()
    {
        return view('livewire.admin.product.upload');
    }
}
