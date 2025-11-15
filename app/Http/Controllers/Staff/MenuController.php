<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display a listing of all menu items.
     */
    public function index(Request $request)
    {
        $query = MenuItem::with('menuCategory');

        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('menu_category_id', $request->category);
        }

        // Filter by availability
        if ($request->has('availability') && $request->availability != '') {
            $query->where('is_available', $request->availability);
        }

        // Search by name or description
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $menuItems = $query->orderBy('menu_category_id')->orderBy('name')->paginate(15);
        $categories = MenuCategory::orderBy('name')->get();

        return view('staff.menu.index', compact('menuItems', 'categories'));
    }

    /**
     * Show the form for creating a new menu item.
     */
    public function create()
    {
        $categories = MenuCategory::orderBy('name')->get();
        return view('staff.menu.create', compact('categories'));
    }

    /**
     * Store a newly created menu item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ingredients' => 'nullable|string',
            'allergens' => 'nullable|string',
            'preparation_time' => 'nullable|integer|min:0',
            'calories' => 'nullable|integer|min:0',
            'is_vegetarian' => 'boolean',
            'is_vegan' => 'boolean',
            'is_gluten_free' => 'boolean',
            'is_dairy_free' => 'boolean',
            'is_spicy' => 'boolean',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        // Convert ingredients and allergens to JSON arrays
        if (isset($validated['ingredients'])) {
            $validated['ingredients'] = json_encode(array_filter(array_map('trim', explode(',', $validated['ingredients']))));
        }
        if (isset($validated['allergens'])) {
            $validated['allergens'] = json_encode(array_filter(array_map('trim', explode(',', $validated['allergens']))));
        }

        MenuItem::create($validated);

        return redirect()->route('staff.menu.index')->with('success', 'Menu item created successfully!');
    }

    /**
     * Show the form for editing the specified menu item.
     */
    public function edit(MenuItem $menuItem)
    {
        $categories = MenuCategory::orderBy('name')->get();
        return view('staff.menu.edit', compact('menuItem', 'categories'));
    }

    /**
     * Update the specified menu item.
     */
    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ingredients' => 'nullable|string',
            'allergens' => 'nullable|string',
            'preparation_time' => 'nullable|integer|min:0',
            'calories' => 'nullable|integer|min:0',
        ]);

        // Handle checkboxes - set to false if not in request
        $validated['is_vegetarian'] = $request->has('is_vegetarian');
        $validated['is_vegan'] = $request->has('is_vegan');
        $validated['is_gluten_free'] = $request->has('is_gluten_free');
        $validated['is_dairy_free'] = $request->has('is_dairy_free');
        $validated['is_spicy'] = $request->has('is_spicy');
        $validated['is_available'] = $request->has('is_available');
        $validated['is_featured'] = $request->has('is_featured');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        // Convert ingredients and allergens to JSON arrays
        if (isset($validated['ingredients'])) {
            $validated['ingredients'] = json_encode(array_filter(array_map('trim', explode(',', $validated['ingredients']))));
        }
        if (isset($validated['allergens'])) {
            $validated['allergens'] = json_encode(array_filter(array_map('trim', explode(',', $validated['allergens']))));
        }

        $menuItem->update($validated);

        return redirect()->route('staff.menu.index')->with('success', 'Menu item updated successfully!');
    }

    /**
     * Remove the specified menu item.
     */
    public function destroy(MenuItem $menuItem)
    {
        // Delete image if exists
        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }

        $menuItem->delete();

        return redirect()->route('staff.menu.index')->with('success', 'Menu item deleted successfully!');
    }

    /**
     * Toggle the availability of a menu item.
     */
    public function toggleAvailability(MenuItem $menuItem)
    {
        $menuItem->update(['is_available' => !$menuItem->is_available]);

        return redirect()->back()->with('success', 'Menu item availability updated!');
    }

    /**
     * Toggle the featured status of a menu item.
     */
    public function toggleFeatured(MenuItem $menuItem)
    {
        $menuItem->update(['is_featured' => !$menuItem->is_featured]);

        return redirect()->back()->with('success', 'Menu item featured status updated!');
    }
}
