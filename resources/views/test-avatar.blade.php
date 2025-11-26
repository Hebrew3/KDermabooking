<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avatar Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Avatar Loading Test</h1>
        
        <div class="space-y-6">
            <!-- User Info -->
            <div class="bg-gray-50 p-4 rounded">
                <h2 class="font-semibold mb-2">User Information:</h2>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Google ID:</strong> {{ $user->google_id ?? 'Not set' }}</p>
            </div>

            <!-- Avatar URLs -->
            <div class="bg-gray-50 p-4 rounded">
                <h2 class="font-semibold mb-2">Avatar URLs:</h2>
                <p><strong>Raw Avatar:</strong> <code class="bg-gray-200 px-2 py-1 rounded text-sm">{{ $user->avatar ?? 'Not set' }}</code></p>
                <p><strong>Profile Picture URL:</strong> <code class="bg-gray-200 px-2 py-1 rounded text-sm">{{ $user->profile_picture_url ?? 'Not set' }}</code></p>
            </div>

            <!-- Image Tests -->
            <div class="space-y-4">
                <h2 class="font-semibold">Image Loading Tests:</h2>
                
                @if($user->profile_picture_url)
                    <!-- Test 1: Direct URL -->
                    <div class="border p-4 rounded">
                        <h3 class="font-medium mb-2">Test 1: Direct Image Load</h3>
                        <img src="{{ $user->profile_picture_url }}" 
                             alt="Avatar Test 1" 
                             class="h-20 w-20 rounded-full border-2 border-gray-300"
                             onload="console.log('Image 1 loaded successfully')"
                             onerror="console.log('Image 1 failed to load'); this.style.border='2px solid red';">
                    </div>

                    <!-- Test 2: With CORS attributes -->
                    <div class="border p-4 rounded">
                        <h3 class="font-medium mb-2">Test 2: With CORS Handling</h3>
                        <img src="{{ $user->profile_picture_url }}" 
                             alt="Avatar Test 2" 
                             class="h-20 w-20 rounded-full border-2 border-gray-300"
                             crossorigin="anonymous"
                             referrerpolicy="no-referrer"
                             onload="console.log('Image 2 loaded successfully')"
                             onerror="console.log('Image 2 failed to load'); this.style.border='2px solid red';">
                    </div>

                    <!-- Test 3: Raw Google URL -->
                    @if($user->avatar)
                    <div class="border p-4 rounded">
                        <h3 class="font-medium mb-2">Test 3: Raw Google URL</h3>
                        <img src="{{ $user->avatar }}" 
                             alt="Avatar Test 3" 
                             class="h-20 w-20 rounded-full border-2 border-gray-300"
                             onload="console.log('Image 3 loaded successfully')"
                             onerror="console.log('Image 3 failed to load'); this.style.border='2px solid red';">
                    </div>
                    @endif

                    <!-- Test 4: Modified URL -->
                    <div class="border p-4 rounded">
                        <h3 class="font-medium mb-2">Test 4: Modified URL (s400)</h3>
                        @php
                            $modifiedUrl = str_replace(['s96-c', 's200-c'], 's400', $user->profile_picture_url);
                        @endphp
                        <img src="{{ $modifiedUrl }}" 
                             alt="Avatar Test 4" 
                             class="h-20 w-20 rounded-full border-2 border-gray-300"
                             onload="console.log('Image 4 loaded successfully')"
                             onerror="console.log('Image 4 failed to load'); this.style.border='2px solid red';">
                        <p class="text-sm text-gray-600 mt-2">URL: <code>{{ $modifiedUrl }}</code></p>
                    </div>
                @else
                    <p class="text-gray-600">No avatar URL available to test.</p>
                @endif
            </div>

            <!-- Browser Console Instructions -->
            <div class="bg-blue-50 border border-blue-200 p-4 rounded">
                <h3 class="font-medium text-blue-800 mb-2">Debug Instructions:</h3>
                <p class="text-blue-700 text-sm">
                    1. Open browser developer tools (F12)<br>
                    2. Check the Console tab for loading messages<br>
                    3. Check the Network tab to see if images are being requested<br>
                    4. Images with red borders failed to load
                </p>
            </div>
        </div>
    </div>

    <script>
        // Additional debugging
        console.log('User ID: {{ $user->id }}');
        console.log('User Name: {{ $user->name }}');
        console.log('Google ID: {{ $user->google_id ?? "Not set" }}');
        console.log('Avatar URL: {{ $user->avatar ?? "Not set" }}');
        console.log('Profile Picture URL: {{ $user->profile_picture_url ?? "Not set" }}');
        
        // Test if we can fetch the image URL directly
        @if($user->profile_picture_url)
        fetch('{{ $user->profile_picture_url }}', { mode: 'no-cors' })
            .then(() => console.log('Fetch test: Image URL is accessible'))
            .catch(error => console.log('Fetch test failed:', error));
        @endif
    </script>
</body>
</html>
