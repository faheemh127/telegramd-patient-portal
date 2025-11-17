# telegramd-patient-portal-plugin

This is a WordPress plugin.

## Installation

After cloning it from github you need to composer install command so you can install the composer dependencies you must have composer install already before running this command

## Required files

You also need the api-keys.php file inside the includes folder that will contain the required API keys for this wordpress plugin

## Production Mode

On production mode make sure this constant HLD_DEVELOPER_ENVIRONMENT should be false true means production environment enabled for this plugin and this is only for testing and development purposes

## How to setup GLP-1-Prefunnel form

[hld_glp_prefunnel form_id="24" pay="later" medications="WwogIHsKICAgICJ0ZWxlZ3JhX2NvZGUiOiAicHZ0OjpiMDRjYWJlNS0yYWNjLTRiOGMtYWFjZC1lZWEzYTQ4YjY1YmIiLAogICAgIm1lZGljYXRpb25fbmFtZSI6ICJUaXJ6ZXBhdGlkZSAoTW9zdCBQb3B1bGFyKSIsCiAgICAiZGVzY3JpcHRpb24iOiAiMjIlIGF2ZyB3ZWlnaHQgbG9zcywgV2Vla2x5IGluamVjdGlvbiAsIER1YWwgYWN0aW9uIEdMUC0xICsgR0lQIiwKICAgICJzdHJpcGVfcHJvZHVjdF9pZCI6ICJwcm9kX1Q2Z1dBR2FkOHZ6dnFVIiwKICAgICJsYWJlbHMiOiAiRnJlZSBFdmFsdWF0aW9uLCBXZWVrbHkgSW5qZWN0aW9uIiwKICAgICJwYWNrYWdlcyI6IFsKICAgICAgewogICAgICAgICJtb250aGx5X2R1cmF0aW9uIjogIjEiLAogICAgICAgICJtb250aGx5X3ByaWNlIjogIjMxMCIsCiAgICAgICAgImRlc2MiOiAiQSBmbGV4aWJsZSBwbGFuIHRvIHN1cHBvcnQgeW91ciB3ZWlnaHQgbG9zcywgb25ldGltZSBwYXltZW50IHJlbmV3IHdoZW4geW91IGNvbmZpZGVudCIKICAgICAgfSwKICAgICAgewogICAgICAgICJtb250aGx5X2R1cmF0aW9uIjogIjMiLAogICAgICAgICJtb250aGx5X3ByaWNlIjogIjI5MCIsCiAgICAgICAgImRlc2MiOiAiRGlzY291bnRlZCByYXRlLCBTYW1lIGJlbmVmaXRzLCBzb21lIGFub3RoZXIgYmVuaWZpdCIKICAgICAgfSwKICAgICAgewogICAgICAgICJtb250aGx5X2R1cmF0aW9uIjogIjYiLAogICAgICAgICJtb250aGx5X3ByaWNlIjogIjI3MCIsCiAgICAgICAgImRlc2MiOiAiQmVzdCB2YWx1ZSBwbGFuIgogICAgICB9CiAgICBdCiAgfSwKICB7CiAgICAidGVsZWdyYV9jb2RlIjogInB2dDo6NDMzMmJjNTItNWY1NC00YzhmLWJmNWMtZjFlZjJiYjk1ZmEwIiwKICAgICJtZWRpY2F0aW9uX25hbWUiOiAiU2VtYWdsdXRpZGUgKEFsdGVybmF0aXZlIE9wdGlvbikiLAogICAgImRlc2NyaXB0aW9uIjogIjE1JSBhdmcgd2VpZ2h0IGxvc3MgLCBXZWVrbHkgaW5qZWN0aW9uICwgUHJvdmVuIEdMUC0xIHBhdGh3YXkiLAogICAgInN0cmlwZV9wcm9kdWN0X2lkIjogInByb2RfVDZnV0FHYWQ4dnp2cVUiLAogICAgImxhYmVscyI6ICJGcmVlIEV2YWx1YXRpb24sIFdlZWtseSBJbmplY3Rpb24iLAogICAgInBhY2thZ2VzIjogWwogICAgICB7CiAgICAgICAgIm1vbnRobHlfZHVyYXRpb24iOiAiMSIsCiAgICAgICAgIm1vbnRobHlfcHJpY2UiOiAiMTMwIiwKICAgICAgICAiZGVzYyI6ICJCZXN0IHZhbHVlIGZvciBtb25leSBhbmQgc2FmdHkgb2YgeW91ciBhbW91bnQsIGFub3RoZXIgZGVzY3JpcHRpb24gZm9yIHRoaXMgcGFja2FnZSIKICAgICAgfSwKICAgICAgewogICAgICAgICJtb250aGx5X2R1cmF0aW9uIjogIjMiLAogICAgICAgICJtb250aGx5X3ByaWNlIjogIjEwNSIsCiAgICAgICAgImRlc2MiOiAiU2F2ZSBtb3JlIG9uIDMtbW9udGggcGxhbiwgUGFpZCBtb250aGx5IG5vIHRvdGFsIHVwZnJvbnQgcGF5bWVudCIKICAgICAgfSwKICAgICAgewogICAgICAgICJtb250aGx5X2R1cmF0aW9uIjogIjYiLAogICAgICAgICJtb250aGx5X3ByaWNlIjogIjk1IiwKICAgICAgICAiZGVzYyI6ICJMb3dlc3QgbW9udGhseSByYXRlLCBzb21lIG90aGVyIGRlc2NyaXB0aW9uIG9mIHRoaXMgcGFja2FnZSIKICAgICAgfQogICAgXQogIH0KXQ=="]

put this shortcode in a page and make sure to put the .hld_glp_1_prefunnel_wrap class to the gutenberg container or may be any elementor container

Install ACF plugin and setup the keys with fluentform form submission so we can backup of each form entries and admin can see them easily

Make sure to composer install after cloning the repo so you can get the required php packages

Make sure to put api-keys.php file rightly so in dev environment you do not miss the files

#use hld_disqualify_step class on the wrapper for fluent form on each step
Make sure that patient dashboard should always be opened in domain.com/my-account url
## Covnert the medications data to base64 for shortcode
there is a file medications.json which can be used to modify and update prices you can convert it to base64 from this tool
https://codebeautify.org/json-to-base64-converter#


## Use this shortcode on prefunnel pages
[hld_glp_prefunnel form_id="3" pay="later" medications=""]
in medications key you should get the base64 data by converting medications.json file which is in plugin and convert it to base64 and then you can use this shortcode in prefunnel pages

## Always include the new prefunnel to the array exists in $telegra_forms array in classfluenthandler


## Fluent forms Backup
All fluent forms latest backup is available to fluent-forms folder of this plugin if you find more fluent form in a backup for example for glp-1-prefunnel folder if you find more than 1 then you can choose the latest date of .json it means previous one are the older backup and the latest date means latest backup you should alwasy prefer to import that


## .hld_form_wrap class
whenever add a fluent form to any wordpress page make sure to add the class .hld_form_wrap as to the container of fluent form shortcode so it can be filled with all the style that is in our custom-style.css file 


Make sure to add hld_glp_prefunnel shortcode and add your forms there in the provided shortcode above and add the medication json in base64 convert them using online tool


## What is quinst-data.json file and where it is used
basically this .json file can be encoded to base64 and pass in the fluent form in hidden field name the name attribute will be telegra_quinst_data and the data will be base64 json from quinst-data.json file. when action form will be submitted this data will be submitted too and our php code will use this data to upload questionnare to telegra using their REST API.


## what is constants.php 
Constant.php file contain the fluent form id's mainly and especially the tables names of the database this plugin creates and uses


## Medication name in shortcode data and Medication name in fluent from dropdown should be same
Make sure that on a specific prefunnel the medication detail that you pass on shortcode (the medication name) that is actually depending on the fluent form medication dropdown behind the scene. bechase we have built a custom UI for selecting medication that's why you need to make sure that both medication names are same including spelling wise and uppercase and lowercase etc