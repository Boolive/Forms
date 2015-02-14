<div class="field field_text<?=$v['error']->bool()?' field_state_error':''?>">
    <label class="field__label" for="id_<?=$v['id']?>"><?=$v['title']?></label><br/>
    <textarea rows="10" style="width: 100%" name="<?=$v['uri']?>" id="id_<?=$v['id']?>" class="field__input input_size_big"><?=$v['value']->string()?></textarea>
    <?php if ($v['error']->bool()): ?>
    <div class="field__error"><?=$v['error']?></div>
    <?php endif; ?>
</div>