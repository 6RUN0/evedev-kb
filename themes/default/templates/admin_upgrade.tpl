{cycle reset=true print=false name=ccl values="kb-table-row-even,kb-table-row-odd"}
{if count($page_error) > 0}
    <div class="block-header2">Error</div>
    {section name=idx loop=$page_error}
    {$page_error[idx]}<br/>
    {/section}
    <br/>
{/if}
<div class="block-header2">Code</div>
{if $codemessage != ''}
    <div class="block-header">Message from the devs</div>
    <p>{$codemessage}</p>
{/if}
<table class="kb-table" width="100%">
    <tr class="kb-table-header">
	<td>Ver</td>
	<td>SVN</td>
	<td>File</td>
	<td>Description</td>
	<td>Action</td>
    </tr>
    {if count($codeList) > 0}
	{section name=idx loop=$codeList}
	    <tr class="{cycle name=ccl}" style="height: 20px">
		<td>
		    {$codeList[idx].version}<br/>
		</td>
		<td>
		    {$codeList[idx].svnrev}<br/>
		</td>
		<td>
		    {$codeList[idx].short_name}<br/>
		</td>
		<td width="50%">
		    {$codeList[idx].desc}<br/>
		</td>
		<td>
		    {if !$codeList[idx].cached || !$codeList[idx].hash_match}
			<a href="?a=admin_upgrade&amp;code_dl_ref={$codeList[idx].version}">Download</a>
			{if !$codeList[idx].hash_match}
			    <span style="text-decoration: blink">!!</span><br/>
			{/if}
		    {/if}
		    {if $codeList[idx].hash_match && $codeList[idx].lowest}
			<a href="?a=admin_upgrade&amp;code_apply_ref={$codeList[idx].version}">Apply</a>
		    {else}
			^<br/>
		    {/if}
		</td>
	    </tr>
	{/section}
    </table>
    <br/><span style="text-decoration: blink">!!</span> - The downloaded file's hash doesn't match the expected one or the file hasn't been downloaded yet.<br/>
    ^ - This patch relies on the one above it.<br/>
    <br/>
{else}
    <tr class="{cycle name=ccl}" style="height: 20px">
	<td colspan="6">No new updates.</td>
    </tr>
    </table>
    <br/>
{/if}
<br/>
<div class="block-header2">Database</div>
{if $DBmessage != ''}
    <div class="block-header">Message from the devs</div>
    <p>{$DBmessage}</p>
{/if}
    <table class="kb-table" width="100%">
    <tr class="kb-table-header">
	<td>Ver</td>
	<td>File</td>
	<td>Description</td>
	<td>Action</td>
    </tr>
    {if count($dbList) > 0}
	{section name=idx loop=$dbList}
	    <tr class="{cycle name=ccl}" style="height: 20px">
		<td>
		    {$dbList[idx].version}<br/>
		</td>
		<td>
		    {$dbList[idx].short_name}<br/>
		</td>
		<td width="50%">
		    {$dbList[idx].desc}<br/>
		</td>
		<td>
		    {if !$dbList[idx].cached || !$dbList[idx].hash_match}
			<a href="?a=admin_upgrade&amp;db_dl_ref={$dbList[idx].version}">Download</a>
			{if !$dbList[idx].hash_match}
			    <span style="text-decoration: blink">!!</span><br/>
			{/if}
		    {/if}
		    {if $dbList[idx].hash_match && $dbList[idx].lowest}
			<a href="?a=admin_upgrade&amp;db_apply_ref={$dbList[idx].version}">Apply</a>
		    {else}
			^<br/>
		    {/if}
		</td>
	    </tr>
	{/section}
    </table>
    <br/><span style="text-decoration: blink">!!</span> - The downloaded file's hash doesn't match the expected one or the file hasn't been downloaded yet.<br/>
    ^ - This patch relies on the one above it.<br/>
    <br/>
    {else}
	<tr class="{cycle name=ccl}" style="height: 20px">
	    <td colspan="5">No new updates.</td>
	</tr>
	</table>
	<br/>
    {/if}
<br/>
The update description file will be retrieved again at: <b>{$update_time} GMT</b><br/>
You can force the update description file to retrieved now by clicking on the <a href="?a=admin_upgrade&amp;refresh">link</a><br/><br/>
Alternatively, you can refresh the page by clicking on the <a href="?a=admin_upgrade">link</a><br/>