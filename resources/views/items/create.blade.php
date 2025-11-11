<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <x-icon :name="$type === 'lost' ? 'exclamation-circle' : 'check-circle'" size="md" class="text-sacli-green-600" />
            {{ ucfirst($type) === 'Lost' ? 'Report Lost Item' : 'Report Found Item' }}
        </h2>
    </x-slot>

    <div class="py-6 lg:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="p-4 sm:p-6 text-gray-900">
                    <!-- Progress Indicator -->
                    <div class="mb-6 sm:mb-8">
                        <div class="flex items-center justify-center">
                            <div class="flex items-center">
                                <div
                                    class="flex items-center justify-center w-6 h-6 sm:w-8 sm:h-8 bg-sacli-green-600 text-white rounded-full text-xs sm:text-sm font-medium shadow-sm">
                                    1
                                </div>
                                <span
                                    class="ml-1 sm:ml-2 text-xs sm:text-sm font-medium text-sacli-green-600 hidden sm:inline">Item
                                    Details</span>
                            </div>
                            <div class="flex-1 h-px bg-gray-200 mx-2 sm:mx-4"></div>
                            <div class="flex items-center">
                                <div
                                    class="flex items-center justify-center w-6 h-6 sm:w-8 sm:h-8 bg-gray-200 text-gray-500 rounded-full text-xs sm:text-sm font-medium">
                                    2
                                </div>
                                <span
                                    class="ml-1 sm:ml-2 text-xs sm:text-sm font-medium text-gray-500 hidden sm:inline">Review
                                    & Submit</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf

                        <!-- Item Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">What would you like to
                                report?</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                <!-- Lost Item Option -->
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="type" value="lost"
                                        {{ old('type', $type) === 'lost' ? 'checked' : '' }}
                                        onchange="updateFormLabels('lost')" class="peer sr-only">
                                    <div
                                        class="border-2 border-gray-300 rounded-xl p-4 sm:p-5 transition-all duration-200 hover:border-gray-400 hover:shadow-md peer-checked:border-sacli-green-600 peer-checked:bg-sacli-green-50 peer-checked:shadow-lg">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0">
                                                <x-icon name="exclamation-circle" size="xl" class="text-red-600" />
                                            </div>
                                            <div>
                                                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Looking for
                                                    (Lost)</h3>
                                                <p class="text-sm text-gray-600 mt-1">Report an item you lost</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Found Item Option -->
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="type" value="found"
                                        {{ old('type', $type) === 'found' ? 'checked' : '' }}
                                        onchange="updateFormLabels('found')" class="peer sr-only">
                                    <div
                                        class="border-2 border-gray-300 rounded-xl p-4 sm:p-5 transition-all duration-200 hover:border-gray-400 hover:shadow-md peer-checked:border-sacli-green-600 peer-checked:bg-sacli-green-50 peer-checked:shadow-lg">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0">
                                                <x-icon name="check-circle" size="xl"
                                                    class="text-sacli-green-600" />
                                            </div>
                                            <div>
                                                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Found</h3>
                                                <p class="text-sm text-gray-600 mt-1">Report an item you found</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('type')" />
                        </div>

                        <!-- Item Title -->
                        <div>
                            <x-input-label for="title" :value="__('Item Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                :value="old('title')" required autofocus icon="document-text" iconPosition="left"
                                placeholder="e.g., Black leather wallet, iPhone 13 Pro, Blue backpack" />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                            <p class="mt-1 text-sm text-gray-600">Provide a clear, descriptive title for the item</p>
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category_id" :value="__('Category')" />
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <x-icon name="tag" size="sm" class="text-gray-400" />
                                </div>
                                <select id="category_id" name="category_id"
                                    class="block w-full pl-10 border-gray-300 focus:border-sacli-green-500 focus:ring-sacli-green-500 rounded-md shadow-sm"
                                    required>
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <div class="relative mt-1">
                                <textarea id="description" name="description" rows="3 sm:rows-4"
                                    class="block w-full border-gray-300 focus:border-sacli-green-500 focus:ring-sacli-green-500 rounded-md shadow-sm"
                                    required placeholder="Provide detailed description including color, size, brand, distinctive features, etc.">{{ old('description') }}</textarea>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            <p class="mt-1 text-sm text-gray-600">Minimum 10 characters. Include as many details as
                                possible to help with identification.</p>
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="block font-medium text-sm text-gray-700">
                                <span
                                    id="location-label">{{ $type === 'lost' ? __('Where did you lose it?') : __('Where did you find it?') }}</span>
                            </label>
                            <x-text-input id="location" name="location" type="text" class="mt-1 block w-full"
                                :value="old('location')" required icon="map-pin" iconPosition="left"
                                placeholder="e.g., Library 2nd floor, Campus cafeteria, Near main gate" />
                            <x-input-error class="mt-2" :messages="$errors->get('location')" />
                        </div>

                        <!-- Date -->
                        <div>
                            <label for="date_occurred" class="block font-medium text-sm text-gray-700">
                                <span
                                    id="date-label">{{ $type === 'lost' ? __('When did you lose it?') : __('When did you find it?') }}</span>
                            </label>
                            <x-text-input id="date_occurred" name="date_occurred" type="date"
                                class="mt-1 block w-full" :value="old('date_occurred')" required max="{{ date('Y-m-d') }}"
                                icon="calendar" iconPosition="left" />
                            <x-input-error class="mt-2" :messages="$errors->get('date_occurred')" />
                        </div>

                        <!-- Images -->
                        <div>
                            <x-input-label for="images" :value="__('Images (Optional)')" />
                            <div class="mt-1">
                                <div
                                    class="flex justify-center px-4 sm:px-6 pt-4 sm:pt-5 pb-4 sm:pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-sacli-green-500 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <x-icon name="photo" size="xl" class="mx-auto text-gray-400" />
                                        <div class="flex text-sm text-gray-600">
                                            <label for="images"
                                                class="relative cursor-pointer bg-white rounded-md font-medium text-sacli-green-600 hover:text-sacli-green-700 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-sacli-green-500">
                                                <span>Upload images</span>
                                                <input id="images" name="images[]" type="file" class="sr-only"
                                                    multiple accept="image/*" onchange="previewImages(this)">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB each (max 5 images)
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('images')" />
                            <x-input-error class="mt-2" :messages="$errors->get('images.*')" />

                            <!-- Image Preview Container -->
                            <div id="image-preview"
                                class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-4 hidden">
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="bg-gray-50 rounded-xl p-4 sm:p-6">
                            <h3
                                class="text-base sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
                                <x-icon name="user-circle" size="md" class="text-sacli-green-600" />
                                Contact Information
                            </h3>
                            <p class="text-sm text-gray-600 mb-3 sm:mb-4">How should people contact you about this
                                item?</p>

                            <!-- Contact Method -->
                            <div class="mb-4">
                                <x-input-label for="contact_method" :value="__('Preferred Contact Method')" />
                                <div class="relative mt-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-icon name="chat-bubble-left-right" size="sm" class="text-gray-400" />
                                    </div>
                                    <select id="contact_method" name="contact_method"
                                        class="block w-full pl-10 border-gray-300 focus:border-sacli-green-500 focus:ring-sacli-green-500 rounded-md shadow-sm"
                                        required onchange="toggleContactFields()">
                                        <option value="">Select contact method</option>
                                        <option value="email"
                                            {{ old('contact_method') === 'email' ? 'selected' : '' }}>Email only
                                        </option>
                                        <option value="phone"
                                            {{ old('contact_method') === 'phone' ? 'selected' : '' }}>Phone only
                                        </option>
                                        <option value="both"
                                            {{ old('contact_method') === 'both' ? 'selected' : '' }}>Both email and
                                            phone</option>
                                    </select>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('contact_method')" />
                            </div>

                            <!-- Email Field -->
                            <div id="email-field" class="mb-4 hidden">
                                <x-input-label for="contact_email" :value="__('Email Address')" />
                                <x-text-input id="contact_email" name="contact_email" type="email"
                                    class="mt-1 block w-full" :value="old('contact_email', auth()->user()->email)" icon="envelope"
                                    iconPosition="left" />
                                <x-input-error class="mt-2" :messages="$errors->get('contact_email')" />
                            </div>

                            <!-- Phone Field -->
                            <div id="phone-field" class="mb-4 hidden">
                                <x-input-label for="contact_phone" :value="__('Phone Number')" />
                                <x-text-input id="contact_phone" name="contact_phone" type="tel"
                                    class="mt-1 block w-full" :value="old('contact_phone')" placeholder="e.g., +1234567890"
                                    icon="phone" iconPosition="left" />
                                <x-input-error class="mt-2" :messages="$errors->get('contact_phone')" />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div
                            class="flex flex-col sm:flex-row items-center justify-between pt-4 sm:pt-6 space-y-3 sm:space-y-0">
                            <a href="{{ route('dashboard') }}"
                                class="text-gray-600 hover:text-gray-800 font-medium text-sm sm:text-base order-2 sm:order-1 inline-flex items-center gap-2 transition-colors">
                                <x-icon name="arrow-left" size="sm" />
                                Back to Dashboard
                            </a>
                            <button type="submit" id="submit-btn"
                                class="w-full sm:w-auto order-1 sm:order-2 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-sacli-green-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-sacli-green-700 focus:bg-sacli-green-700 active:bg-sacli-green-800 focus:outline-none focus:ring-2 focus:ring-sacli-green-500 focus:ring-offset-2 transition-all duration-200 ease-in-out shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                                <x-icon name="paper-airplane" size="sm" id="submit-icon" />
                                <span id="submit-text">{{ __('Submit Item Report') }}</span>
                                <span id="submit-spinner" class="hidden">
                                    <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for form interactions -->
    <script>
        function updateFormLabels(type) {
            const locationLabel = document.getElementById('location-label');
            const dateLabel = document.getElementById('date-label');

            if (type === 'lost') {
                locationLabel.textContent = 'Where did you lose it?';
                dateLabel.textContent = 'When did you lose it?';
            } else {
                locationLabel.textContent = 'Where did you find it?';
                dateLabel.textContent = 'When did you find it?';
            }
        }

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
                            <button type="button" onclick="removeImage(this, ${index})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors shadow-sm">
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

            // Handle form submission loading state
            const form = document.querySelector('form');
            const submitBtn = document.getElementById('submit-btn');
            const submitIcon = document.getElementById('submit-icon');
            const submitText = document.getElementById('submit-text');
            const submitSpinner = document.getElementById('submit-spinner');

            form.addEventListener('submit', function(e) {
                // Show loading state
                submitBtn.disabled = true;
                submitIcon.classList.add('hidden');
                submitSpinner.classList.remove('hidden');
                submitText.textContent = 'Submitting...';
            });
        });
    </script>
</x-app-layout>
