<td>
    @if ($visit->verified_by)
        {{ $visit->getVerifiedByName->name }}
    @endif
</td>
