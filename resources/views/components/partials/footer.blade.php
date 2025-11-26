<!-- Footer -->
<footer class="bg-gray-900 text-white py-16 relative overflow-hidden">
    <!-- Soft gradient overlay for depth -->
    <div class="absolute inset-0 bg-gradient-to-br from-pink-900/10 via-gray-900 to-rose-900/10 opacity-80"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
            <!-- Brand Section -->
            <div class="md:col-span-2">
                <div class="flex items-center mb-5">
                    <img src="{{ asset('images/logo1.jpg') }}" alt="K-Derma Logo"
                        class="h-12 w-12 rounded-lg object-cover shadow-md ring-2 ring-pink-500/30">
                    <span
                        class="ml-3 text-2xl font-bold tracking-wide bg-gradient-to-r from-pink-400 to-rose-400 bg-clip-text text-transparent">
                        K-DERMA
                    </span>
                </div>
                <p class="text-gray-400 leading-relaxed max-w-md">
                    Expert dermatology and advanced skincare treatments to help you glow with confidence.
                </p>

                <!-- Social Icons -->
                <div class="flex space-x-4 mt-6">
                    <a href="#"
                        class="w-10 h-10 bg-pink-500/10 hover:bg-pink-500/20 rounded-full flex items-center justify-center text-pink-400 hover:text-pink-300 ">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M22.46 6c-.77.35-1.6.59-2.46.7a4.26 4.26 0 001.88-2.36 8.48 8.48 0 01-2.7 1.04 4.23 4.23 0 00-7.2 3.86A12 12 0 013 4.8a4.23 4.23 0 001.3 5.65 4.19 4.19 0 01-1.91-.52v.05a4.23 4.23 0 003.39 4.14 4.24 4.24 0 01-1.9.07 4.24 4.24 0 003.95 2.93A8.5 8.5 0 012 19.54 12 12 0 008.29 21c7.55 0 11.68-6.26 11.68-11.68 0-.18 0-.35-.01-.53A8.3 8.3 0 0022.46 6z" />
                        </svg>
                    </a>
                    <a href="#"
                        class="w-10 h-10 bg-pink-500/10 hover:bg-pink-500/20 rounded-full flex items-center justify-center text-pink-400 hover:text-pink-300 ">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2.04c-5.52 0-10 4.48-10 10a10 10 0 0014.25 8.89v-6.28h-2.95v-2.61h2.95V9.42c0-2.91 1.78-4.49 4.37-4.49 1.24 0 2.31.09 2.62.13v3.03h-1.8c-1.41 0-1.68.67-1.68 1.64v2.14h3.36l-.44 2.61h-2.92v6.28A10 10 0 0012 2.04z" />
                        </svg>
                    </a>
                    <a href="#"
                        class="w-10 h-10 bg-pink-500/10 hover:bg-pink-500/20 rounded-full flex items-center justify-center text-pink-400 hover:text-pink-300 ">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M21.5 6.5a2.7 2.7 0 01-1.9.8 2.75 2.75 0 001.2-1.5 5.4 5.4 0 01-1.7.7 2.7 2.7 0 00-4.6 2.5 7.7 7.7 0 01-5.6-2.8 2.7 2.7 0 00.8 3.6 2.6 2.6 0 01-1.2-.3v.03a2.7 2.7 0 002.2 2.7 2.8 2.8 0 01-1.2.05 2.7 2.7 0 002.5 1.9A5.4 5.4 0 016 18.4 7.7 7.7 0 0010.2 20c6.3 0 9.8-5.3 9.8-9.8v-.4a6.8 6.8 0 001.5-1.5z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Services -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-pink-300">Services</h3>
                <ul class="space-y-2 text-gray-400">
                    @php
                        $featuredServices = \App\Models\Service::active()
                            ->featured()
                            ->orderBy('sort_order')
                            ->orderBy('name')
                            ->limit(4)
                            ->get();
                    @endphp
                    
                    @forelse($featuredServices as $service)
                        <li><a href="#services" class="hover:text-pink-400">{{ $service->name }}</a></li>
                    @empty
                        <li><a href="#services" class="hover:text-pink-400">Facial Treatments</a></li>
                        <li><a href="#services" class="hover:text-pink-400">Laser Therapy</a></li>
                        <li><a href="#services" class="hover:text-pink-400">Skin Consultation</a></li>
                        <li><a href="#services" class="hover:text-pink-400">Chemical Peels</a></li>
                    @endforelse
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-pink-300">Contact</h3>
                <ul class="space-y-2 text-gray-400">
                    <li>Poblacion 4, City of Calaca</li>
                    <li>Batangas, Philippines</li>
                    <li><a href="tel:09525829243" class="hover:text-pink-400 ">0952 582 9243</a></li>
                    <li><a href="mailto:deasisd82@gmail.com" class="hover:text-pink-400 ">deasisd82@gmail.com</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-500 text-sm">
            <p>
                &copy; 2025
                <span class="font-semibold text-white">K-Derma</span>.
                All rights reserved.
            </p>
        </div>
    </div>
</footer>
