<div class="image-container {{ implode(' ', array_keys(array_filter(compact('withBorder', 'withBackground', 'stretched')))) }}">
    <img src="{{ $file['url'] }}" alt="{{ $caption }}" />
    @if (!empty($caption))
        <div class="image-caption">{{ $caption }}</div>
    @endif
</div>
