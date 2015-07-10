<div class="field field_text<?=$v['error']->bool()?' field_state_error':''?>">
    <label class="field__label" for="id_<?=$v['id']?>"><?=$v['title']?></label>
    <div class="field__input-wrap">
    <textarea rows="10" style="width: 100%" name="<?=$v['uri']?>" id="id_<?=$v['id']?>" class="field__input field__input_textarea input_size_big"><?=$v['value']->string()?></textarea>
    </div>
    <?php if ($v['error']->bool()): ?>
    <div class="field__error"><?=$v['error']?></div>
    <?php endif; ?>
</div>