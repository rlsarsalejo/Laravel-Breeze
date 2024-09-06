<x-app-layout>
    <x-slot name="header">
        <div class="flex gap-5">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-4">Welcome to the Dashboard</h2>

                    <!-- Flash Message for Success -->
                    <div id="flash-message" class="mb-4 p-4 hidden"></div>

                    <!-- Form to Add Member -->
                    <div class="modal-container fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
                        <div class="modal-content bg-white p-6 rounded shadow-md w-full max-w-md relative">
                           
                            
                            <form id="add-member-form" class="flex flex-col gap-4">
                                @csrf
                                <h3 class="text-xl font-semibold mb-4 text-center">Add a New Member</h3>

                                <!-- Name Field -->
                                <div class="flex flex-col">
                                    <label for="name" class="block text-gray-700">Name</label>
                                    <input type="text" id="name" name="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <p id="name-error" class="text-red-500 text-sm mt-1 hidden"></p>
                                </div>

                                <!-- Email Field -->
                                <div class="flex flex-col">
                                    <label for="email" class="block text-gray-700">Email</label>
                                    <input type="email" id="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <p id="email-error" class="text-red-500 text-sm mt-1 hidden"></p>
                                </div>

                                <!-- Address Field -->
                                <div class="flex flex-col">
                                    <label for="address" class="block text-gray-700">Address</label>
                                    <input type="text" id="address" name="address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <p id="address-error" class="text-red-500 text-sm mt-1 hidden"></p>
                                </div>

                                <!-- Phone Number Field -->
                                <div class="flex flex-col">
                                    <label for="phoneNumber" class="block text-gray-700">Phone Number</label>
                                    <input type="number" id="phoneNumber" name="phoneNumber" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <p id="phoneNumber-error" class="text-red-500 text-sm mt-1 hidden"></p>
                                </div>

        
                                 <div class="flex justify-end gap-2 ">
                                     <!-- Submit Button -->
                                 <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    Add Member
                                    </button>
                                     <!-- Close Button -->
                                    
                                    <button class=" close-modal px-4 py-2 bg-red-400 text-white rounded hover:bg-red-500" aria-label="Close">Cancel</button>
                                 </div>
                               
                            </form>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-between">
                        <h3 class="text-xl font-semibold">Members List</h3>
                        <button id="view-members" class="hover:text-red-300 font-semibold text-xl bg-blue-400 p-2 text-gray-800 leading-tight">View Members</button>
                        <button id="open-form" class="hover:text-red-300 font-semibold text-xl bg-green-400 p-2 text-gray-800 leading-tight">Add Member</button>
                    </div>
                    <div id="members-list" class="mt-6">
                        <ul id="members" class="list-disc pl-5"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('add-member-form');
            const flashMessage = document.getElementById('flash-message');
            const nameError = document.getElementById('name-error');
            const emailError = document.getElementById('email-error');
            const addressError = document.getElementById('address-error');
            const phoneNumberError = document.getElementById('phoneNumber-error');
            const membersList = document.getElementById('members');
            const viewMembersBtn = document.getElementById('view-members');
            const openFormBtn = document.getElementById('open-form');
            const modalContainer = document.querySelector('.modal-container');
            const closeModalBtn = document.querySelector('.close-modal');

            // Show the form modal
            openFormBtn.addEventListener('click', () => {
                modalContainer.classList.remove('hidden');
            });

            // Close the form modal
            closeModalBtn.addEventListener('click', () => {
                modalContainer.classList.add('hidden');
            });

            // Handle form submission to add a member
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                // Get form data
                const formData = new FormData(form);
                const data = {
                    name: formData.get('name'),
                    email: formData.get('email'),
                    address: formData.get('address'),
                    phoneNumber: formData.get('phoneNumber')
                };

                try {
                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Make API request
                    const response = await fetch('{{ url('/api/members') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (response.ok) {
                        flashMessage.textContent = result.message;
                        flashMessage.className = 'mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded';
                        flashMessage.classList.remove('hidden');
                        form.reset();
                        modalContainer.classList.add('hidden');
                    } else {
                        // Display validation errors
                        if (result.errors) {
                            nameError.textContent = result.errors.name ? result.errors.name.join(', ') : '';
                            emailError.textContent = result.errors.email ? result.errors.email.join(', ') : '';
                            addressError.textContent = result.errors.address ? result.errors.address.join(', ') : '';
                            phoneNumberError.textContent = result.errors.phoneNumber ? result.errors.phoneNumber.join(', ') : '';

                            nameError.classList.toggle('hidden', !result.errors.name);
                            emailError.classList.toggle('hidden', !result.errors.email);
                            addressError.classList.toggle('hidden', !result.errors.address);
                            phoneNumberError.classList.toggle('hidden', !result.errors.phoneNumber);
                        }
                        flashMessage.textContent = result.message || 'An error occurred';
                        flashMessage.className = 'mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded';
                        flashMessage.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    flashMessage.textContent = 'An error occurred';
                    flashMessage.className = 'mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded';
                    flashMessage.classList.remove('hidden');
                }
            });

            // Handle button click to fetch members
            viewMembersBtn.addEventListener('click', async () => {
                try {
                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Make API request to get members
                    const response = await fetch('{{ url('/api/members') }}', {
                        method: 'GET',
                        headers: {
                            'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    const result = await response.json();

                    if (response.ok) {
                        // Clear previous members
                        membersList.innerHTML = '';

                        // Append new members
                        result.data.forEach(member => {
                            const listItem = document.createElement('li');
                            listItem.textContent = `${member.name} - ${member.email}`;
                            membersList.appendChild(listItem);
                        });
                    } else {
                        flashMessage.textContent = result.message || 'An error occurred';
                        flashMessage.className = 'mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded';
                        flashMessage.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    flashMessage.textContent = 'An error occurred';
                    flashMessage.className = 'mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded';
                    flashMessage.classList.remove('hidden');
                }
            });
        });
    </script>
</x-app-layout>
