<div class="link-tool">
    <a class="link-tool__content link-tool__content--rendered" target="_blank" rel="nofollow noindex noreferrer" href="{{ $link }}">
        @if (!empty($meta['image']['url']))
            <div class="link-tool__image" style="background-image: url('{{ $meta['image']['url'] }}');"></div>
        @endif
        <div class="link-tool__title">{{ $meta['title'] }}</div>
        <p class="link-tool__description">{{ $meta['description'] }}</p>
        <span class="link-tool__anchor">{{ $link }}</span>
    </a>
</div>
