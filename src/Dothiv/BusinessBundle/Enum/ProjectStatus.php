<?php

namespace Dothiv\BusinessBundle\Enum;

/**
 * This class contains an enumeration of possible states of a project.
 */
class ProjectStatus {
    const DRAFT = 0;
    const SUBMITTED = 1;
    const CLOSED = 2;
    const ACCEPTED = 3;
    const PUBLISHED = 4;
    const FAILED = 5;
    const FUNDED = 6;
    const COMPLETED = 7;
}
