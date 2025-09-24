<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ ucfirst($type) === 'Lost' ? 'Report Lost Item' : 'Report Found Item' }}
        </h2>
    </x-slot>

    <div class="py-6 lg:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    <!-- Progress Indicator -->
                    <div class="mb-6 sm:mb-8">
                        <div class="flex items-center justify-center">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-6 h-6 sm:w-8 sm:h-8 bg-sacli-green-500 text-white rounded-full text-xs sm:text-sm font-medium">
                                    1
                                </div>
                                <span class="ml-1 sm:ml-2 text-xs sm:text-sm font-medium text-sacli-green-600 hidden sm:inline">Item Details</span>
                            </div>
                            <div class="flex-1 h-px bg-gray-200 mx-2 sm:mx-4"></div>
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-6 h-6 sm:w-8 sm:h-8 bg-gray-200 text-gray-500 rounded-full text-xs sm:text-sm font-medium">
                                    2
                                </div>
                                <span class="ml-1 sm:ml-2 text-xs sm:text-sm font-medium text-gray-500 hidden sm:inline">Review & Submit</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">

                        <!-- Item Type Display -->
                        <div class="bg-sacli-green-50 border border-sacli-green-200 rounded-lg p-3 sm:p-4">
                            <div class="flex items-start sm:items-center">
                                @if($type === 'lost')
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sacli-green-600 mr-2 sm:mr-3 flex-shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.220 0-4.239.086-6.306.315C3.612 15.45 2 16.414 2 17.657V18a2 2 0 002 2h16a2 2 0 002-2v-.343c0-1.243-1.612-2.207-3.694-2.342C16.239 15.086 14.220 15 12 15z" />
                                    </svg>
                                    <div>
                                        <h3 class="text-base sm:text-lg font-medium text-sacli-green-800">Reporting a Lost Item</h3>
                                        <p class="text-sm sm:text-base text-sacli-green-700">Provide details about the item you lost so others can help you find it.</p>
                                    </div>
                                @else
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sacli-green-600 mr-2 sm:mr-3 flex-shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <h3 class="text-base sm:text-lg font-medium text-sacli-green-800">Reporting a Found Item</h3>
                                        <p class="text-sm sm:text-base text-sacli-green-700">Help someone find their lost item by providing details about what you found.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Item Title -->
                        <div>
                            <x-input-label for="title" :value="__('Item Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" 
                                :value="old('title')" required autofocus 
                                placeholder="e.g., Black leather wallet, iPhone 13 Pro, Blue backpack" />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                            <p class="mt-1 text-sm text-gray-600">Provide a clear, descriptive title for the item</p>
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 focus:border-sacli-green-500 focus:ring-sacli-green-500 rounded-md shadow-sm" required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3 sm:rows-4" 
                                class="mt-1 block w-full border-gray-300 focus:border-sacli-green-500 focus:ring-sacli-green-500 rounded-md shadow-sm" 
                                required placeholder="Provide detailed description including color, size, brand, distinctive features, etc.">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            <p class="mt-1 text-sm text-gray-600">Minimum 10 characters. Include as many details as possible to help with identification.</p>
                        </div>

                        <!-- Location -->
                        <div>
                            <x-input-label for="location" :value="$type === 'lost' ? __('Where did you lose it?') : __('Where did you find it?')" />
                            <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" 
                                :value="old('location')" required 
                                placeholder="e.g., Library 2nd floor, Campus cafeteria, Near main gate" />
                            <x-input-error class="mt-2" :messages="$errors->get('location')" />
                        </div>

                        <!-- Date -->
                        <div>
                            <x-input-label for="date_occurred" :value="$type === 'lost' ? __('When did you lose it?') : __('When did you find it?')" />
                            <x-text-input id="date_occurred" name="date_occurred" type="date" class="mt-1 block w-full" 
                                :value="old('date_occurred')" required max="{{ date('Y-m-d') }}" />
                            <x-input-error class="mt-2" :messages="$errors->get('date_occurred')" />
                        </div>

                        <!-- Images -->
                        <div>
                            <x-input-label for="images" :value="__('Images (Optional)')" />
                            <div class="mt-1">
                                <div class="flex justify-center px-4 sm:px-6 pt-4 sm:pt-5 pb-4 sm:pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-sacli-green-400 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-8 w-8 sm:h-12 sm:w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-sacli-green-600 hover:text-sacli-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-sacli-green-500">
                                                <span>Upload images</span>
                                                <input id="images" name="images[]" type="file" class="sr-only" multiple accept="image/*" onchange="previewImages(this)">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB each (max 5 images)</p>
                                    </div>
                                </div>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('images')" />
                            <x-input-error class="mt-2" :messages="$errors->get('images.*')" />
                            
                            <!-- Image Preview Container -->
                            <div id="image-preview" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-4 hidden"></div>
                        </div>

                        <!-- Contact Information -->
                        <div class="bg-gray-50 rounded-lg p-4 sm:p-6">
                            <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Contact Information</h3>
                            <p class="text-sm text-gray-600 mb-3 sm:mb-4">How should people contact you about this item?</p>

                            <!-- Contact Method -->
                            <div class="mb-4">
                                <x-input-label for="contact_method" :value="__('Preferred Contact Method')" />
                                <select id="contact_method" name="contact_method" class="mt-1 block w-full border-gray-300 focus:border-sacli-green-500 focus:ring-sacli-green-500 rounded-md shadow-sm" required onchange="toggleContactFields()">
                                    <option value="">Select contact method</option>
                                    <option value="email" {{ old('contact_method') === 'email' ? 'selected' : '' }}>Email only</option>
                                    <option value="phone" {{ old('contact_method') === 'phone' ? 'selected' : '' }}>Phone only</option>
                                    <option value="both" {{ old('contact_method') === 'both' ? 'selected' : '' }}>Both email and phone</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('contact_method')" />
                            </div>

                            <!-- Email Field -->
                            <div id="email-field" class="mb-4 hidden">
                                <x-input-label for="contact_email" :value="__('Email Address')" />
                                <x-text-input id="contact_email" name="contact_email" type="email" class="mt-1 block w-full" 
                                    :value="old('contact_email', auth()->user()->email)" />
                                <x-input-error class="mt-2" :messages="$errors->get('contact_email')" />
                            </div>

                            <!-- Phone Field -->
                            <div id="phone-field" class="mb-4 hidden">
                                <x-input-label for="contact_phone" :value="__('Phone Number')" />
                                <x-text-input id="contact_phone" name="contact_phone" type="tel" class="mt-1 block w-full" 
                                    :value="old('contact_phone')" placeholder="e.g., +1234567890" />
                                <x-input-error class="mt-2" :messages="$errors->get('contact_phone')" />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex flex-col sm:flex-row items-center justify-between pt-4 sm:pt-6 space-y-3 sm:space-y-0">
                            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800 font-medium text-sm sm:text-base order-2 sm:order-1">
                                ← Back to Dashboard
                            </a>
                            <x-primary-button class="w-full sm:w-auto order-1 sm:order-2 bg-sacli-green-600 hover:bg-sacli-green-700 focus:bg-sacli-green-700 active:bg-sacli-green-900 focus:ring-sacli-green-500">
                                {{ __('Submit Item Report') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for form interactions -->
    <script>
        function toggleContactFields() {
            const method = document.getElementById('contact_method').value;
            const emailField = document.getElementById('email-field');
            const phoneField = document.getElementById('phone-field');
            
            emailField.classList.add('hidden');
            phoneField.classList.add('hidden');
            
            if (method === 'email' || method === 'both') {
                emailField.classList.remove('hidden');
            }
            if (method === 'phone' || method === 'both') {
                phoneField.classList.remove('hidden');
            }
        }

        function previewImages(input) {
            const previewContainer = document.getElementById('image-preview');
            previewContainer.innerHTML = '';
            
            if (input.files && input.files.length > 0) {
                previewContainer.classList.remove('hidden');
                
                Array.from(input.files).forEach((file, index) => {
                    if (index >= 5) return; // Limit to 5 images
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border border-gray-200">
                            <button type="button" onclick="removeImage(this, ${index})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                ×
                            </button>
                        `;
                        previewContainer.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                previewContainer.classList.add('hidden');
            }
        }

        function removeImage(button, index) {
            const input = document.getElementById('images');
            const dt = new DataTransfer();
            
            Array.from(input.files).forEach((file, i) => {
                if (i !== index) {
                    dt.items.add(file);
                }
            });
            
            input.files = dt.files;
            previewImages(input);
        }

        // Initialize contact fields based on old input
        document.addEventListener('DOMContentLoaded', function() {
            toggleContactFields();
        });
    </script>
</x-app-layout>