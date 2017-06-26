<section class="scroll">
    <ul class="list">
        {$presence = getPresencesTxt()}
        <li class="subheader">
            <p>
                <span class="info">{$list|count}</span>
                {$c->__('chatrooms.users')}
            </p>
        </li>
        {loop="$list"}
            <li class="{if="$value->last > 60"} inactive{/if}"
                title="{$value->resource}">
                {$url = $value->getPhoto('s')}
                {if="$url"}
                    <span class="primary icon bubble status {$presence[$value->value]}">
                        <img src="{$url}">
                    </span>
                {else}
                    <span class="primary icon bubble color {$value->resource|stringToColor} status {$presence[$value->value]}">
                        <i class="zmdi zmdi-account"></i>
                    </span>
                {/if}
                {if="$value->mucaffiliation =='owner'"}
                    <span class="control icon gray">
                        <i class="zmdi zmdi-star"></i>
                    </span>
                {/if}
                {if="$value->mucjid && strpos($value->mucjid, '/') == false && !$c->supported('anonymous')"}
                    <p class="line normal">
                        <a href="{$c->route('contact', $value->mucjid)}">{$value->resource}</a>
                    </p>
                {else}
                    <p class="line normal">{$value->resource}</p>
                {/if}
                {if="$value->status"}
                    <p class="line">{$value->status}</p>
                {/if}
            </li>
        {/loop}
    </ul>
</section>
<div>
    <button onclick="Dialog_ajaxClear()" class="button flat">
        {$c->__('button.close')}
    </button>
</div>
