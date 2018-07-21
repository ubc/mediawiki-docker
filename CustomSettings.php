<?php
################
#  Namespaces  #
################
## Please rememver to define a constants for each namespace. Since
## SemanticMediaWiki uses the namespaces 100-109, the ids should
## start from 110 for our own custom namespaces. Even ids denotes a
## content NS, whereas odd ids denotes a duscussion NS (talk pages).

## Course Namepsace
define("NS_COURSE", 110);
define("NS_COURSE_TALK", 111);

$wgExtraNamespaces[NS_COURSE] = "Course";
$wgExtraNamespaces[NS_COURSE_TALK] = "Course_talk";


## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_COURSE] = true;
$wgNamespacesWithSubpages[NS_COURSE_TALK] = true;

$wgContentNamespaces[] = NS_COURSE;


## Documentation Namespace
define("NS_DOCUMENTATION", 112);
define("NS_DOCUMENTATION_TALK", 113);

$wgExtraNamespaces[NS_DOCUMENTATION] = "Documentation";
$wgExtraNamespaces[NS_DOCUMENTATION_TALK] = "Documentation_talk";


## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_DOCUMENTATION] = true;
$wgNamespacesWithSubpages[NS_DOCUMENTATION_TALK] = true;
$wgContentNamespaces[] = NS_DOCUMENTATION;


## Notepad Namespace
define("NS_NOTEPAD", 114);
define("NS_NOTEPAD_TALK", 115);

$wgExtraNamespaces[NS_NOTEPAD] = "Notepad";
$wgExtraNamespaces[NS_NOTEPAD_TALK] = "Notepad_talk";

$wgNamespacesToBeSearchedDefaulT[NS_NOTEPAD_TALK] = false;

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_NOTEPAD] = true;
$wgNamespacesWithSubpages[NS_NOTEPAD_TALK] = true;
$wgContentNamespaces[] = NS_NOTEPAD;


## YouTube Namespace
define("NS_YOUTUBE", 116);
define("NS_YOUTUBE_TALK", 117);

$wgExtraNamespaces[NS_YOUTUBE] = "YouTube";
$wgExtraNamespaces[NS_YOUTUBE_TALK] = "YouTube_talk";


## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_YOUTUBE] = false;
$wgNamespacesWithSubpages[NS_YOUTUBE_TALK] = false;
$wgContentNamespaces[] = NS_YOUTUBE;

## Elearning Namespace
define("NS_ELEARNING", 118);
define("NS_ELEARNING_TALK", 119);

$wgExtraNamespaces[NS_ELEARNING] = "Elearning";
$wgExtraNamespaces[NS_ELEARNING_TALK] = "Elearning_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_ELEARNING] = true;
$wgNamespacesWithSubpages[NS_ELEARNING_TALK] = false;

$wgContentNamespaces[] = NS_ELEARNING;

// Only Elearning Group can edit.
$wgNamespaceProtection[NS_ELEARNING] = array('elearning');

## Elearning User Group ##
// this group can can edit elearning
$wgGroupPermissions['elearning']['elearning'] = true;

$wgNamespacesWithSubpages[NS_HELP] = true;
$wgNamespacesWithSubpages[NS_HELP] = true;

## Library Namespace
define("NS_LIBRARY", 120);
define("NS_LIBRARY_TALK", 121);

$wgExtraNamespaces[NS_LIBRARY] = "Library";
$wgExtraNamespaces[NS_LIBRARY_TALK] = "Library_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_LIBRARY] = true;
$wgNamespacesWithSubpages[NS_LIBRARY_TALK] = false;

$wgContentNamespaces[] = NS_LIBRARY;

// Only Elearning Group can edit.
$wgNamespaceProtection[NS_LIBRARY] = array('library');

## Library User Group ##
// this group can can edit Library
$wgGroupPermissions['library']['library'] = true;
$wgGroupPermissions['library']['reupload'] = true;
## Library Manager Group ##
## This group can add users to the Library Group ##
// this group can can edit Library
$wgGroupPermissions['library_manager']['library'] = true;
// Library Manager can add users to library and linbrary_manager
$wgAddGroups['library_manager'] = array('library','library_manager');
// Library Manager can add users
$wgRemoveGroups['library_manager'] = array('library','library_manager');

## Notepad SANDBOX
define("NS_SANDBOX", 122);
define("NS_SANDBOX_TALK", 123);

$wgExtraNamespaces[NS_SANDBOX] = "Sandbox";
$wgExtraNamespaces[NS_SANDBOX_TALK] = "Sandbox_talk";

$wgNamespacesToBeSearchedDefaulT[NS_SANDBOX_TALK] = false;

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_SANDBOX] = true;
$wgNamespacesWithSubpages[NS_SANDBOX_TALK] = true;

$wgContentNamespaces[] = NS_SANDBOX;

## Arts Group / Namespace ##
define("NS_ARTS", 124);
define("NS_ARTS_TALK", 125);

$wgExtraNamespaces[NS_ARTS] = "Arts";
$wgExtraNamespaces[NS_ARTS_TALK] = "Arts_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_ARTS] = true;
$wgNamespacesWithSubpages[NS_ARTS_TALK] = false;

$wgContentNamespaces[] = NS_ARTS;

// Only Arts  Group can edit.
$wgNamespaceProtection[NS_ARTS] = array('arts');

## Arts User Group ##
// this group can can edit Arts
$wgGroupPermissions['arts']['arts'] = true;

## Learning Commons Group / Namespace ##
define("NS_LEARNINGCOMMONS", 126);
define("NS_LEARNINGCOMMONS_TALK", 127);

$wgExtraNamespaces[NS_LEARNINGCOMMONS] = "Learning_Commons";
$wgExtraNamespaces[NS_LEARNINGCOMMONS_TALK] = "Learning_Commons_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_LEARNINGCOMMONS] = true;
$wgNamespacesWithSubpages[NS_LEARNINGCOMMONS_TALK] = false;

$wgContentNamespaces[] = NS_LEARNINGCOMMONS;

## LEARNING COMMONS User Group ##
// this group can can edit Learning Commons
$wgGroupPermissions['learning_commons']['learning_commons'] = true;

// Only LEARNING COMMONS Group can edit.
//$wgNamespaceProtection[NS_LEARNINGCOMMONS] = array('learning_commons');

## LFS Land and Food System Namespace
define("NS_LFS", 128);
define("NS_LFS_TALK", 129);

$wgExtraNamespaces[NS_LFS] = "LFS";
$wgExtraNamespaces[NS_LFS_TALK] = "LFS_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_LFS] = true;
$wgNamespacesWithSubpages[NS_LFS_TALK] = false;

$wgContentNamespaces[] = NS_LFS;

// Only LFS Group can edit.
$wgNamespaceProtection[NS_LFS] = array('lfs');

## LFS User Group ##
// this group can can edit LFS
$wgGroupPermissions['lfs']['lfs'] = true;

## LFS Manager Group ##
## This group can add users to the LFS Group ##
// this group can can edit LFS
$wgGroupPermissions['lfs_manager']['lfs'] = true;
// LFS Manager can add users to lfs and lfs_manager
$wgAddGroups['lfs_manager'] = array('lfs','lfs_manager');
// LFS Manager can add users
$wgRemoveGroups['lfs_manager'] = array('lfs','lfs_manager');

## END LFS Land and Food System Namespace

## Science Namespace
define("NS_SCIENCE", 130);
define("NS_SCIENCE_TALK", 131);

$wgExtraNamespaces[NS_SCIENCE] = "Science";
$wgExtraNamespaces[NS_SCIENCE_TALK] = "Science_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_SCIENCE] = true;
$wgNamespacesWithSubpages[NS_SCIENCE_TALK] = false;

$wgContentNamespaces[] = NS_SCIENCE;

// Only Elearning Group can edit.
$wgNamespaceProtection[NS_SCIENCE] = array('science');

## Science User Group ##
// this group can can edit Library
$wgGroupPermissions['science']['science'] = true;

## Science Manager Group ##
## This group can add users to the Science Group ##
// this group can can edit Science
$wgGroupPermissions['science_manager']['science'] = true;
// Science Manager can add users to science and science_manager
$wgAddGroups['science_manager'] = array('science','science_manager');
// Science Manager can add users
$wgRemoveGroups['science_manager'] = array('science','science_manager');

# Define the THREAD NAMESPACE
define("NS_THREAD", 90);
define("NS_THREAD_TALK", 91);

# Define the SUMMARY  NAMESPACE
define("NS_SUMMARY", 92);
define("NS_SUMMARY_TALK", 93);

## Namespace COPYRIGHT
define("NS_COPYRIGHT", 140);
define("NS_COPYRIGHT_TALK", 141);

$wgExtraNamespaces[NS_COPYRIGHT] = "Copyright";
$wgExtraNamespaces[NS_COPYRIGHT_TALK] = "Copyright_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_COPYRIGHT] = true;
$wgNamespacesWithSubpages[NS_COPYRIGHT_TALK] = false;

$wgContentNamespaces[] = NS_COPYRIGHT;

// Only Copyright Group can edit.
$wgNamespaceProtection[NS_COPYRIGHT] = array('copyright');

## Copyright User Group ##
// this group can can edit Library
$wgGroupPermissions['copyright']['copyright'] = true;

## Cpyright Manager Group ##
## This group can add users to the Copyright Group ##
// this group can can edit Copyright
$wgGroupPermissions['copyright_manager']['copyright'] = true;
//  Copyright Manager can add users to copyright and copyright_manager
$wgAddGroups['copyright_manager'] = array('copyright','copyright_manager');
// Copyright Manager can add users
$wgRemoveGroups['copyright_manager'] = array('copyright','copyright_manager');

## Search these Namespances by default
$wgNamespacesToBeSearchedDefault = array(
        NS_MAIN           => true,
        NS_USER           => true,
        NS_HELP           => true,
        NS_COURSE         => true,
        NS_COURSE_TALK    => true,
        NS_THREAD         => true,
        NS_SUMMARY        => true,
        NS_DOCUMENTATION  => true,
        NS_FILE           => true,
        NS_CATEGORY       => true,
        NS_YOUTUBE        => true,
        NS_ELEARNING      => true,
        NS_LIBRARY        => true,
        NS_NOTEPAD        => true,
        NS_ARTS           => true,
        NS_LEARNINGCOMMONS => true,
        NS_LFS             => true,
        NS_SCIENCE         => true,
        NS_COURSE          => true,
);
#####################
## End Namepsace Area
#####################

$wgAllowExternalImages = true;
