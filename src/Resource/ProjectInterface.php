<?php

namespace Devops\Resource;

interface ProjectInterface
{
    public function getLatestCommitSha($branch = 'master'): string;
}
