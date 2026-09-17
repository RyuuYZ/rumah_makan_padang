<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Services\WebpUploadService;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    private function formatFotoUrl(?string $foto): ?string
    {
        if (empty($foto)) {
            return null;
        }

        if (str_starts_with($foto, 'http://') || str_starts_with($foto, 'https://')) {
            return $foto;
        }

        return url($foto);
    }

    public function index(Request $request)
    {
        $query = MenuItem::query()->where('is_active', true)->with('category');

        if ($request->has('kategori') && $request->kategori !== 'all') {
            $slug = $request->kategori;
            $query->whereHas('category', function ($q) use ($slug) {
                $q->where('slug', $slug);
            });
        }

        if ($request->has('branch_id')) {
            // Get menu with prices for specific branch
            $menuItems = $query->with(['branchPrices' => function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id)->where('is_available', true);
            }])->get();

            return response()->json([
                'success' => true,
                'data' => $menuItems->map(function ($item) {
                    $price = $item->branchPrices->first();
                    $fotoUrl = $this->formatFotoUrl($item->foto);

                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'kategori' => $item->kategori,
                        'deskripsi' => $item->deskripsi,
                        'foto' => $fotoUrl,
                        'foto_path' => $item->foto,
                        'foto_format' => str_ends_with(strtolower((string) $item->foto), '.webp') ? 'webp' : 'image',
                        'badge' => $item->badge,
                        'rating' => (float) $item->rating,
                        'harga' => $price ? (int) $price->harga : null,
                        'harga_display' => $price ? 'Rp '.number_format((int) $price->harga, 0, ',', '.') : null,
                    ];
                }),
            ]);
        }

        // Without price info
        $menuItems = $query->get();

        return response()->json([
            'success' => true,
            'data' => $menuItems->map(fn ($item) => [
                'id' => $item->id,
                'nama' => $item->nama,
                'kategori' => $item->kategori,
                'deskripsi' => $item->deskripsi,
                'foto' => $this->formatFotoUrl($item->foto),
                'foto_path' => $item->foto,
                'foto_format' => str_ends_with(strtolower((string) $item->foto), '.webp') ? 'webp' : 'image',
                'badge' => $item->badge,
                'rating' => (float) $item->rating,
            ]),
        ]);
    }

    public function show(string $id)
    {
        $menuItem = MenuItem::with('branchPrices.branch')->findOrFail($id);

        $data = $menuItem->toArray();
        $data['foto_url'] = $this->formatFotoUrl($menuItem->foto);
        $data['foto_format'] = str_ends_with(strtolower((string) $menuItem->foto), '.webp') ? 'webp' : 'image';

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function uploadImage(Request $request, WebpUploadService $webpUploadService)
    {
        $request->validate([
            'image' => 'required|image|mimes:webp,png,jpg,jpeg|max:2048',
        ]);

        $path = $webpUploadService->uploadAndConvertToWebp($request->file('image'), 'menu');

        return response()->json([
            'success' => true,
            'message' => 'Image successfully uploaded and converted to WebP format',
            'data' => [
                'path' => $path,
                'url' => url($path),
                'format' => 'webp',
            ],
        ]);
    }
}
