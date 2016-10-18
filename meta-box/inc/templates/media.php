<script id="tmpl-swpmb-media-item" type="text/html">
  <input type="hidden" name="{{{ data.fieldName }}}" value="{{{ data.id }}}" class="swpmb-media-input">
  <div class="swpmb-media-preview">
    <div class="swpmb-media-content">
      <div class="centered">
        <# if ( 'image' === data.type && data.sizes ) { #>
          <# if ( data.sizes.thumbnail ) { #>
            <img src="{{{ data.sizes.thumbnail.url }}}">
          <# } else { #>
            <img src="{{{ data.sizes.full.url }}}">
          <# } #>
        <# } else { #>
          <# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
            <img src="{{ data.image.src }}" />
          <# } else { #>
            <img src="{{ data.icon }}" />
          <# } #>
        <# } #>
      </div>
    </div>
  </div>
  <div class="swpmb-media-info">
    <h4>
      <a href="{{{ data.url }}}" target="_blank" title="{{{ i18nSwpmbMedia.view }}}">
        <# if( data.title ) { #> {{{ data.title }}}
          <# } else { #> {{{ i18nSwpmbMedia.noTitle }}}
        <# } #>
      </a>
    </h4>
    <p>{{{ data.mime }}}</p>
    <p>
      <a class="swpmb-edit-media" title="{{{ i18nSwpmbMedia.edit }}}" href="{{{ data.editLink }}}" target="_blank">
        <span class="dashicons dashicons-edit"></span>{{{ i18nSwpmbMedia.edit }}}
      </a>
      <a href="#" class="swpmb-remove-media" title="{{{ i18nSwpmbMedia.remove }}}">
        <span class="dashicons dashicons-no-alt"></span>{{{ i18nSwpmbMedia.remove }}}
      </a>
    </p>
  </div>
</script>

<script id="tmpl-swpmb-media-status" type="text/html">
	<# if ( data.maxFiles > 0 ) { #>
		{{{ data.items }}}/{{{ data.maxFiles }}}
		<# if ( 1 < data.maxFiles ) { #>  {{{ i18nSwpmbMedia.multiple }}} <# } else {#> {{{ i18nSwpmbMedia.single }}} <# } #>
	<# } #>
</script>
