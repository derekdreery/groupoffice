{if $sdesc != ''}{$sdesc|default:''}{/if}
{if $desc != ''}{$desc|default:''}{/if}
{if count($tags) > 0}
<h4>Tags:</h4>
<div class="tags">
<table border="0" cellspacing="0" cellpadding="0">
{section name=tag loop=$tags}
  <tr>
    <td><b>{$tags[tag].keyword}:</b>&nbsp;&nbsp;</td><td>{$tags[tag].data}</td>
  </tr>
{/section}
</table>
</div>
{/if}
