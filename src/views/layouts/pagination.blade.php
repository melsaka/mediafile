@if ($paginator->hasPages())
<!-- pagination -->
<nav aria-label="Page navigation" >
  	<ul class="flex items-center -space-x-px h-10 text-base">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
		    <li>
		      	<span aria-disabled="true" aria-label="previous" class="flex items-center justify-center px-4 h-10 ml-0 leading-tight rounded-l-lg bg-dash-300 mr-2 rounded-md text-gray-500">
		        	<span class="sr-only">Previous</span>
		        	<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
		          		<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
		        	</svg>
		      	</span>
		    </li>
        @else
		    <li>
		      	<a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="previous" class="flex items-center justify-center px-4 h-10 ml-0 leading-tight rounded-l-lg bg-dash-300 mr-2 rounded-md text-gray-400 hover:bg-gray-800 hover:text-white">
		        	<span class="sr-only">Previous</span>
		        	<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
		          		<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
		        	</svg>
		      	</a>
		    </li>
        @endif
      
        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            {{-- @if (is_string($element))
                <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
            @endif --}}
            
	        @if (is_array($element))
		        @foreach ($element as $page => $url)
		            @if ($page == $paginator->currentPage())
					    <li>
					      <span aria-current="page" class="z-10 flex items-center justify-center px-4 h-10 leading-tight mr-2 rounded-md bg-blue-800/[.8] text-white">{{ $page }}</span>
					    </li>
		            @else
		            	@if($page > ($paginator->currentPage() - 2) && $page < ($paginator->currentPage() + 2))
					    <li>
					      <a href="{{ $url }}" class="flex items-center justify-center px-4 h-10 leading-tight bg-dash-300 mr-2 rounded-md text-gray-400 hover:bg-gray-800 hover:text-white">{{ $page }}</a>
					    </li>
					    @endif
		            @endif
		        @endforeach
		    @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
		    <li>
		      	<a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="next" class="flex items-center justify-center px-4 h-10 leading-tight rounded-r-lg bg-dash-300 mr-2 rounded-md text-gray-400 hover:bg-gray-800 hover:text-white">
			        <span class="sr-only">Next</span>
			        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
			          	<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
			        </svg>
		      	</a>
		    </li>
        @else
		    <li>
		      	<span aria-disabled="true" aria-label="next" class="flex items-center justify-center px-4 h-10 leading-tight rounded-r-lg bg-dash-300 mr-2 rounded-md text-gray-500">
		        	<span class="sr-only">Next</span>
			        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
			          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
			        </svg>
		      	</span>
		    </li>
        @endif
  	</ul>
</nav>
@endif
