<div class="card shadow mb-3 {{ $class }}" id="{{ $id }}" style="{{ $style }}">
  <div class="card-header"><i class="{{ $icon }}"></i> {{ $header }}</div>
  <div class="card-body">
    <h5 class="card-title">{{ $title }}</h5>
    {{ $slot }}
  </div>
</div>