<?php

function gravatar_url($id)
{
    return "https://i.pravatar.cc/50?img=" . http_build_query([
        'img' => $id
    ]);
}