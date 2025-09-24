<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-sacli-green-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm border border-sacli-green-200 sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-sacli-green-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-sacli-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-sacli-green-800">Welcome back!</h3>
                            <p class="text-gray-600">{{ __("You're logged in and ready to help reunite people with their belongings.") }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <a href="{{ route('items.create', ['type' => 'lost']) }}" class="block p-4 bg-sacli-green-50 border border-sacli-green-200 rounded-lg hover:bg-sacli-green-100 transition duration-150 ease-in-out">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-sacli-green-600 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-sacli-green-800">Report Lost Item</h4>
                                    <p class="text-sm text-sacli-green-600">Lost something? Let others know</p>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('items.create', ['type' => 'found']) }}" class="block p-4 bg-sacli-green-50 border border-sacli-green-200 rounded-lg hover:bg-sacli-green-100 transition duration-150 ease-in-out">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-sacli-green-600 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-sacli-green-800">Report Found Item</h4>
                                    <p class="text-sm text-sacli-green-600">Found something? Help return it</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
