<?php

namespace Phpoar;

function Some($val): Option {
    return Option::_some($val);
}

function None(): Option {
    return Option::_none();
}

function Ok($val): Result {
    return Result::_ok($val);
}

function Err($err): Result {
    return Result::_err($err);
}