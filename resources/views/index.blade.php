<x-layout>

<section id="home" class="relative bg-gradient-to-br from-pink-50 via-white to-rose-50 overflow-hidden">
    <!-- Decorative gradient blur elements -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-pink-300/30 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-rose-200/30 rounded-full blur-3xl"></div>

    <div class="relative z-10 flex items-center min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-24 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <!-- Text Content -->
            <div class="space-y-8">
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 leading-tight">
                    Beautiful Skin
                    <span class="bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">Starts
                        Here</span>
                </h1>

                <p class="text-lg md:text-xl text-gray-600 leading-relaxed max-w-lg">
                    Discover advanced dermatology treatments and personalized skincare solutions
                    designed to rejuvenate your skin, restore your glow, and help you feel confident in your own
                    beauty.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="#services"
                        class="bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-8 py-4 rounded-full text-lg font-semibold  shadow-lg hover:shadow-rose-200/80 text-center">
                        Explore Services
                    </a>
                    <a href="#about"
                        class="border-2 border-pink-500 text-pink-600 hover:bg-pink-500 hover:text-white px-8 py-4 rounded-full text-lg font-semibold  text-center">
                        Learn More
                    </a>
                </div>
            </div>

            <!-- Image -->
            <div class="relative group">
                <div class="relative overflow-hidden rounded-3xl shadow-2xl ring-1 ring-gray-100  ">
                    <img src="{{ asset('images/landing2.jpg') }}" alt="K-Derma Professional Skincare"
                        class="w-full h-[480px] lg:h-[540px] object-cover  ">
                </div>

            </div>
        </div>
    </div>
</section>

<section id="services" class="py-24 bg-gradient-to-br from-white via-pink-50/50 to-rose-50 relative overflow-hidden">
    <div
        class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top_right,_rgba(255,192,203,0.15),_transparent_60%)]">
    </div>

    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 mb-4">Our Signature Services</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Experience expert care with advanced skincare solutions tailored to your beauty and confidence.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($services as $service)
                <div class="group bg-white rounded-3xl p-8 shadow-md hover:shadow-2xl  border border-pink-100 ">
                    @if ($service->image_url)
                        <div class="w-20 h-20 rounded-2xl overflow-hidden mb-6 mx-auto">
                            <img src="{{ $service->image_url }}" alt="{{ $service->name }}"
                                class="w-full h-full object-cover  ">
                        </div>
                    @else
                        <div
                            class="w-20 h-20 bg-gradient-to-r from-pink-500 to-rose-500 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                        </div>
                    @endif
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3 text-center">{{ $service->name }}</h3>
                    <p class="text-gray-600 text-center mb-6">
                        {{ $service->short_description ?? Str::limit($service->description, 100) }}
                    </p>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-pink-600">{{ $service->formatted_price }}</div>
                        @if ($service->duration)
                            <div class="text-sm text-gray-500 mt-1">{{ $service->formatted_duration }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <!-- Static fallback cards -->
                <div class="group bg-white rounded-3xl p-8 shadow-md hover:shadow-2xl  border border-pink-100 ">
                    <div
                        class="w-20 h-20 bg-gradient-to-r from-pink-500 to-rose-500 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3 text-center">Facial Treatments</h3>
                    <p class="text-gray-600 text-center mb-6">Deep cleansing and hydrating facials for glowing
                        skin.</p>
                    <div class="text-center text-2xl font-bold text-pink-600">From ₱2,500</div>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-16">
            @auth
                <a href="{{ route('appointments.book') }}"
                    class="inline-block bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-10 py-4 rounded-full text-lg font-semibold shadow-md hover:shadow-rose-200/80 ">
                    Book Your Appointment
                </a>
            @else
                <a href="{{ route('register') }}"
                    class="inline-block bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-10 py-4 rounded-full text-lg font-semibold shadow-md hover:shadow-rose-200/80 ">
                    Book Your Appointment
                </a>
            @endauth
        </div>
    </div>
</section>

<section id="about" class="py-24 bg-gradient-to-br from-pink-50 via-white to-rose-50 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
        <div class="space-y-8">
            <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900">About <span
                    class="bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">K-Derma</span>
            </h2>
            <p class="text-lg text-gray-600 leading-relaxed">
                Established in 2022 along Calaca Highway, <strong>K-Derma</strong> is a state-of-the-art beauty and
                dermatology clinic offering a full range of treatments—from rejuvenating facials to advanced
                aesthetic procedures—delivered by certified professionals using cutting-edge equipment.
            </p>
            <p class="text-lg text-gray-600 leading-relaxed">
                We combine science and luxury to provide you with a personalized experience that enhances both your
                beauty and confidence in a serene, spa-like setting.
            </p>
        </div>

        <div class="relative">
            <div class="bg-white rounded-3xl shadow-2xl p-10 space-y-8 ring-1 ring-pink-100">
                <div class="flex items-start space-x-5">
                    <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-lg">Certified Professionals</h3>
                        <p class="text-gray-600">Licensed dermatologists and expert skincare specialists.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-5">
                    <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-lg">Advanced Technology</h3>
                        <p class="text-gray-600">State-of-the-art aesthetic equipment and modern techniques.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-5">
                    <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-lg">Personalized Care</h3>
                        <p class="text-gray-600">Tailored skincare programs for every individual.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="contact" class="py-24 bg-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 mb-4">Get In Touch</h2>
            <p class="text-lg text-gray-600">Your journey to radiant, healthy skin begins here.</p>
        </div>

        <div class="max-w-2xl mx-auto">
            <!-- Contact Info -->
            <div class="space-y-8">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-lg">Address</h3>
                        <p class="text-gray-600">Poblacion 4, City of Calaca</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-lg">Phone</h3>
                        <p class="text-gray-600">0952 582 9243</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-lg">Email</h3>
                        <p class="text-gray-600">deasisd82@gmail.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- K Derma Chatbot Assistant -->
<x-chatbot />

</x-layout>