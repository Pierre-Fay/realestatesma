<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Property;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Public landing page: a hero slider of featured listings plus a search bar.
     */
    public function index(): View
    {
        $slides = Property::query()
            ->where('is_active', true)
            ->where('is_sold', false)
            ->with(['images', 'categories'])
            ->orderByDesc('is_featured')
            ->latest()
            ->limit(6)
            ->get();

        return view('home', [
            'slides' => $slides,
            'categories' => Category::filterOptions(),
        ]);
    }
}
