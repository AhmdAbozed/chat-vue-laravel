<?php

namespace MyProject\Enums;

enum ChannelType: String
{
    
  case private = 'private';
  case APPROVED = '1';
  case REJECTED = '3';
}