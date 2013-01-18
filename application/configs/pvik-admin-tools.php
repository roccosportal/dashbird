<?php
self::$Config['PvikAdminTools'] = array();
self::$Config['PvikAdminTools']['Url'] = '/admin/';
self::$Config['PvikAdminTools']['Login']= array ();
self::$Config['PvikAdminTools']['Login']['Username'] = 'Admin';
self::$Config['PvikAdminTools']['Login']['PasswordMD5'] = '098f6bcd4621d373cade4e832627b4f6';
self::$Config['PvikAdminTools']['FileFolders']= array ('~/files/', '~/images/');

self::$Config['PvikAdminTools']['Tables'] = array (
    'Users' => array (
            'Fields' => array (
                'Name' => array ('Type' => 'Normal', 'ShowInOverview' => true),
                'Password' => array ('Type' => 'Password', 'ShowInOverview' => true)
             ),
             'ForeignTables' => array('UserShares' => array ('ForeignKey' => 'UserId'))
    ),
    'UserShares' => array (
        'Fields' => array (
                'User' => array ('Type' => 'Select', 'UseField' => 'Name', 'ShowInOverview' => true),
                'ConnectedUser' => array ('Type' => 'Select', 'UseField' => 'Name', 'ShowInOverview' => true)
          ),
    ),
    'Entries' => array (
            'Fields' => array (
                'EntryId' => array ('Type' => 'Primary', 'ShowInOverview' => true),
                'Text' => array ('Type' => 'Wysiwyg', 'ShowInOverview' => false),
                'DateTime' => array ('Type' => 'Date', 'ShowInOverview' => true),
                'SearchHelper' => array ('Type' => 'Normal', 'ShowInOverview' => false, 'Nullable' => true),
                'User' => array ('Type' => 'Select', 'UseField' => 'Name', 'ShowInOverview' => true)
             ),
             'ForeignTables' => array(
                 'Comments' => array ('ForeignKey' => 'EntryId', 'ShowCountInOverview' => true),
                 'EntriesTags' => array ('ForeignKey' => 'EntryId', 'ShowCountInOverview' => true),
              )
    ),
    'Comments' => array (
            'Fields' => array (
                'Text' => array ('Type' => 'Textarea', 'ShowInOverview' => true),
                'DateTime' => array ('Type' => 'Normal', 'ShowInOverview' => true),
                'User' => array ('Type' => 'Select', 'UseField' => 'Name', 'ShowInOverview' => true)
             )

    ),
    'Tags' => array (
            'Fields' => array (
                'Title' => array ('Type' => 'Normal', 'ShowInOverview' => true),
             )

    ),
    'EntriesTags' => array (
            'Fields' => array (
                'Tag' => array ('Type' => 'Select', 'UseField' => 'Title', 'ShowInOverview' => true),
                'Entry' => array ('Type' => 'Select', 'UseField' => 'EntryId', 'ShowInOverview' => true)
             )
    ),
             
  
);

