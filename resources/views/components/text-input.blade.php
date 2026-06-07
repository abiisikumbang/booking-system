@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-gray-700 text-gray-300 placeholder:text-gray-500 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
