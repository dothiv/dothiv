<?php

namespace Dothiv\BusinessBundle;

final class BusinessEvents
{
    const DOMAIN_REGISTERED = 'dothiv.business.domain.registered';

    const DOMAIN_TRANSFERRED = 'dothiv.business.domain.transferred';

    const CLAIM_TOKEN_REQUESTED = 'dothiv.business.domain.claim_token_requested';

    const DOMAIN_DELETED = 'dothiv.business.domain.deleted';

    const USER_LOGINLINK_REQUESTED = 'dothiv.business.user.loginlink.requested';

    const CLICKCOUNTER_CONFIGURATION = 'dothiv.basewebsite.clickcounter.configuration';

    const USER_EMAIL_CHANGED = 'dothiv.business.user.email.changed';

    const ENTITY_CHANGED = 'dothiv.business.entity.changed';

    const ENTITY_CREATED = 'dothiv.business.entity.created';
}
