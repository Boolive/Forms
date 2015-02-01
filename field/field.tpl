<div class="field<?=$v['error']->bool()?' field_state_error':''?>">
    <label class="field__label" for="id_<?=$v['id']?>"><?=$v['title']?></label>
    <input class="field__input input_size_big" id="id_<?=$v['id']?>" name="<?=$v['uri']?>" value="<?=$v['value']->escape()?>" type="text">
    <?php if ($v['error']->bool()): ?>
    <div class="field__error"><?=$v['error']?></div>
    <?php endif; ?>
</div>