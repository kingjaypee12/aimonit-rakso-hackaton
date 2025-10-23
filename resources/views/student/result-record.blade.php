@extends('components.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-sky-50 to-blue-100 py-10 print:bg-white">
    <div class="max-w-5xl mx-auto px-4">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 print:hidden">
            <h1 class="text-3xl md:text-4xl font-extrabold text-blue-700 mb-4 md:mb-0">
                🌈 Fun Learning Flashcards
            </h1>

            <div class="flex space-x-3">
                <button onclick="window.print()"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-full shadow-md transition">
                    🖨️ Print
                </button>

                <a href="{{ url()->previous() }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-full shadow-md transition">
                    ⬅️ Back
                </a>
            </div>
        </div>

        {{-- FLASHCARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 print:grid-cols-2 print:gap-4">
            @foreach($flashcards as $card)
                <div
                    class="bg-white border-2 border-blue-200 rounded-2xl p-6 flex flex-col items-center text-center shadow-md hover:shadow-xl transition hover:-translate-y-1 print:shadow-none print:border print:rounded-xl print:p-4 break-inside-avoid-page">
                    
                    <div class="text-6xl mb-3 animate-bounce-slow print:animate-none">
                        {{ $card->emoji }}
                    </div>

                    <h2 class="text-lg font-bold text-blue-700 mb-2">
                        {{ $card->title }}
                    </h2>

                    <p class="text-gray-700 text-base leading-relaxed">
                        {{ $card->tip }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- FOOTER --}}
        <div class="text-center mt-12 print:hidden">
            <p class="text-gray-600 text-sm">
                ✨ Keep learning and having fun! ✨
            </p>
        </div>
    </div>
</div>

{{-- Minimal Tailwind Animation --}}
@push('styles')
<style>
@keyframes bounce-slow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-6px); }
}
.animate-bounce-slow {
  animation: bounce-slow 2.5s infinite;
}
@media print {
  .print\:hidden { display: none !important; }
  .print\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .print\:gap-4 { gap: 1rem; }
  .print\:rounded-xl { border-radius: 0.75rem; }
  .print\:border { border: 1px solid #cbd5e1; }
  .print\:p-4 { padding: 1rem; }
  .break-inside-avoid-page { break-inside: avoid-page; page-break-inside: avoid; }
}
</style>
@endpush
@endsection