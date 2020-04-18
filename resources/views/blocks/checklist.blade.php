<div class="cdx-block cdx-checklist">
    @foreach ($items as $item)
        <div class="cdx-checklist__item cdx-checklist__item{{ $item['checked'] ? '--checked' : '' }}">
            <span class="cdx-checklist__item-checkbox"></span>
            <div class="cdx-checklist__item-text">{{ $item['text'] }}</div>
        </div>
    @endforeach
</div>
