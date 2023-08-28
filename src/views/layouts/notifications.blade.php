@if (isset($errors) && $errors->any())
<ul class="fixed top-20 right-0 px-4 lg:px-0 md:right-12 w-full md:max-w-md lg:max-w-lg xl:max-w-xl z-60">

    @foreach ($errors->all() as $error)
	<li class="flex items-center w-full p-4 mb-4 text-gray-500 bg-red-100 rounded-lg shadow" role="alert">
	    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg">
	        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
	            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
	        </svg>
	        <span class="sr-only">Error icon</span>
	    </div>
	    <div class="mr-3 text-sm font-semibold text-red-700">{{ $error }}</div>
	    <button type="button" class="ms-auto close-notification -mx-1.5 -my-1.5 bg-red-100 text-red-700 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-red-50 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#toast-danger" aria-label="Close">
	        <span class="sr-only">Close</span>
	        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
	            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
	        </svg>
	    </button>
	</li>
    @endforeach
</ul>
@endif

@if(session()->has('success'))
<ul class="fixed top-20 right-0 px-4 lg:px-0 md:right-12 w-full md:max-w-md lg:max-w-lg xl:max-w-xl z-60">
	<li class="flex items-center w-full p-4 mb-4 text-gray-500 bg-green-100 rounded-lg shadow" role="alert">
	    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
	        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
	            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
	        </svg>
	        <span class="sr-only">Check icon</span>
	    </div>
	    <div class="mr-3 text-sm font-semibold text-green-800">{{ session()->get('success') }}</div>
	    <button type="button" class="ms-auto close-notification -mx-1.5 -my-1.5 text-green-800 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-green-50 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#toast-danger" aria-label="Close">
	        <span class="sr-only">Close</span>
	        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
	            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
	        </svg>
	    </button>
	</li>
</ul>
@endif

@if(session()->has('status'))
<ul class="fixed top-20 right-0 px-4 lg:px-0 md:right-12 w-full md:max-w-md lg:max-w-lg xl:max-w-xl z-60">
	<li class="flex items-center w-full p-4 mb-4 text-gray-500 bg-green-100 rounded-lg shadow" role="alert">
	    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
	        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
	            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
	        </svg>
	        <span class="sr-only">Check icon</span>
	    </div>
	    <div class="mr-3 text-sm font-semibold text-green-800">{{ session()->get('status') }}</div>
	    <button type="button" class="ms-auto close-notification -mx-1.5 -my-1.5 text-green-800 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-green-50 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#toast-danger" aria-label="Close">
	        <span class="sr-only">Close</span>
	        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
	            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
	        </svg>
	    </button>
	</li>
</ul>
@endif
