<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Categories') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    Back to Dashboard
                </a>
                <button onclick="openCategoryModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    Add New Category
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($categories->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($categories as $category)
                                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200" data-category-id="{{ $category->id }}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-3">
                                                @if($category->icon)
                                                    <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3" 
                                                         style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
                                                        <i class="{{ $category->icon }} text-lg"></i>
                                                    </div>
                                                @else
                                                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center mr-3">
                                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h3 class="text-lg font-medium text-gray-900">{{ $category->name }}</h3>
                                                    <p class="text-sm text-gray-500">{{ $category->items_count }} items</p>
                                                </div>
                                            </div>
                                            
                                            @if($category->description)
                                                <p class="text-gray-600 text-sm mb-4">{{ $category->description }}</p>
                                            @endif
                                            
                                            <div class="flex items-center space-x-2 text-xs text-gray-500">
                                                <span class="inline-flex items-center">
                                                    <div class="w-3 h-3 rounded-full mr-1" style="background-color: {{ $category->color }}"></div>
                                                    {{ $category->color }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex-shrink-0 ml-4">
                                            <div class="flex space-x-2">
                                                <button onclick="editCategory({{ $category->id }})" 
                                                        class="text-emerald-600 hover:text-emerald-900 text-sm font-medium">
                                                    Edit
                                                </button>
                                                @if($category->items_count == 0)
                                                    <button onclick="deleteCategory({{ $category->id }})" 
                                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                        Delete
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No categories</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first category.</p>
                            <div class="mt-6">
                                <button onclick="openCategoryModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    Add Category
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    <div id="category-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="category-modal-title">Add New Category</h3>
                    <button onclick="closeCategoryModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="category-form">
                    <input type="hidden" id="category-id" name="category_id">
                    
                    <div class="mb-4">
                        <label for="category-name" class="block text-sm font-medium text-gray-700 mb-2">
                            Category Name *
                        </label>
                        <input type="text" id="category-name" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="Enter category name">
                    </div>
                    
                    <div class="mb-4">
                        <label for="category-description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="category-description" name="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                                  placeholder="Enter category description"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label for="category-icon" class="block text-sm font-medium text-gray-700 mb-2">
                            Icon Class (Optional)
                        </label>
                        <input type="text" id="category-icon" name="icon"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="e.g., fas fa-mobile-alt">
                        <p class="text-xs text-gray-500 mt-1">Use Font Awesome or similar icon classes</p>
                    </div>
                    
                    <div class="mb-6">
                        <label for="category-color" class="block text-sm font-medium text-gray-700 mb-2">
                            Color
                        </label>
                        <div class="flex items-center space-x-3">
                            <input type="color" id="category-color" name="color" value="#10B981"
                                   class="w-12 h-10 border border-gray-300 rounded cursor-pointer">
                            <input type="text" id="category-color-text" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="#10B981" value="#10B981">
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCategoryModal()" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition duration-150 ease-in-out">
                            Cancel
                        </button>
                        <button type="submit" id="category-submit-btn"
                                class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-md transition duration-150 ease-in-out">
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let editingCategoryId = null;
        
        // Color picker synchronization
        document.addEventListener('DOMContentLoaded', function() {
            const colorPicker = document.getElementById('category-color');
            const colorText = document.getElementById('category-color-text');
            
            colorPicker.addEventListener('change', function() {
                colorText.value = this.value;
            });
            
            colorText.addEventListener('input', function() {
                if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                    colorPicker.value = this.value;
                }
            });
        });
        
        function openCategoryModal(categoryId = null) {
            editingCategoryId = categoryId;
            const modal = document.getElementById('category-modal');
            const title = document.getElementById('category-modal-title');
            const submitBtn = document.getElementById('category-submit-btn');
            
            // Reset form
            document.getElementById('category-form').reset();
            document.getElementById('category-id').value = '';
            document.getElementById('category-color').value = '#10B981';
            document.getElementById('category-color-text').value = '#10B981';
            
            if (categoryId) {
                title.textContent = 'Edit Category';
                submitBtn.textContent = 'Update Category';
                
                // Load category data (in real implementation, this would be an AJAX call)
                loadCategoryData(categoryId);
            } else {
                title.textContent = 'Add New Category';
                submitBtn.textContent = 'Save Category';
            }
            
            modal.classList.remove('hidden');
        }
        
        function closeCategoryModal() {
            document.getElementById('category-modal').classList.add('hidden');
            editingCategoryId = null;
        }
        
        function loadCategoryData(categoryId) {
            // In a real implementation, this would fetch data via AJAX
            // For now, we'll simulate loading from the DOM
            const categoryElement = document.querySelector(`[data-category-id="${categoryId}"]`);
            if (categoryElement) {
                const name = categoryElement.querySelector('h3').textContent;
                const description = categoryElement.querySelector('p.text-gray-600')?.textContent || '';
                
                document.getElementById('category-id').value = categoryId;
                document.getElementById('category-name').value = name;
                document.getElementById('category-description').value = description;
            }
        }
        
        function editCategory(categoryId) {
            openCategoryModal(categoryId);
        }
        
        function deleteCategory(categoryId) {
            if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                return;
            }
            
            fetch(`/admin/categories/${categoryId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const categoryElement = document.querySelector(`[data-category-id="${categoryId}"]`);
                    if (categoryElement) {
                        categoryElement.style.opacity = '0.5';
                        setTimeout(() => {
                            categoryElement.remove();
                        }, 500);
                    }
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while deleting the category', 'error');
            });
        }
        
        // Handle category form submission
        document.getElementById('category-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const categoryId = formData.get('category_id');
            const isEditing = categoryId && categoryId !== '';
            
            const url = isEditing ? `/admin/categories/${categoryId}` : '/admin/categories';
            const method = isEditing ? 'PUT' : 'POST';
            
            // Show loading state
            const submitBtn = document.getElementById('category-submit-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;
            
            // Prepare data
            const data = {
                name: formData.get('name'),
                description: formData.get('description'),
                icon: formData.get('icon'),
                color: formData.get('color')
            };
            
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeCategoryModal();
                    showNotification(data.message, 'success');
                    
                    // Reload the page to show updated categories
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while saving the category', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
        
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
                type === 'success' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-red-100 text-red-800 border border-red-200'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Remove after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
    @endpush
</x-app-layout>