{{--
    Form aksi yang meminta konfirmasi sebelum dikirim.

    Form hanya membawa atribut data; dialog konfirmasi tunggal dibuat
    satu kali pada layout admin sehingga konsisten di seluruh halaman.
--}}
@props([
    'action',
    'method' => 'POST',
    'label',
    'title',
    'message',
    'confirmLabel' => 'Ya, Lanjutkan',
    'tone' => 'danger',
    'class' => 'inline',
    'buttonClass' => 'bg-primary text-white px-3 py-1.5 rounded-md text-xs hover:bg-primary-dark',
    'slot' => null,
])

<form action="{{ $action }}"
      method="POST"
      class="{{ $class }}"
      data-confirm-title="{{ $title }}"
      data-confirm-message="{{ $message }}"
      data-confirm-button="{{ $confirmLabel }}"
      data-confirm-tone="{{ $tone }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    {{ $slot }}

    <button type="submit" class="{{ $buttonClass }}">{{ $label }}</button>
</form>