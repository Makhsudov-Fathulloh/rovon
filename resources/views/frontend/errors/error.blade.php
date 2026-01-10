<x-frontend.layouts.blank title="{{ $code ?? 'Хатолик' }}">
    <div class="container text-center py-5" style="font-family: sans-serif; color: whitesmoke">
        <h1 class="display-1 mt-5 mb-3">Хатолик {{ $code ?? '' }}</h1>
        <p class="fs-2">{{ $message ?? 'Сайтда техник носозлик юз берди. Илтимос, кейинроқ уриниб кўринг.' }}</p>
    </div>
</x-frontend.layouts.blank>
