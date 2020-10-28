# SchoolBridge CDN Client Uploader #
### PHP 7.0+ ###

This is the PHP client library to upload files to the SchoolBridge CDN.

### Requirements ###
* PHP 7.0 or greater with curl compiled
* SB_CDN_AUTH_USERNAME - provided by Inbox Design
* SB_CDN_AUTH_PASSWORD - provided by Inbox Design

### Installation ###

```
composer require schoolbridge/sbcdn
```

### Example ###

```
<?php 
	
	$file = 'path/to/my/image.jpg';
	
	$sbcdn = new SBCDN();
	
	$upload = $sbcdn
			->add($file)
			->destination('tidewater/requests/') // school 'permalink' then folder path
			->name('file_'. time() .'.jpg') // optional
			->upload();
	
```
### Response ###

*Success:* 200 Okay Response code

*Error:* 400 code with message parameters


```
	[sbcdn:response] => Object(
		[status] => 200
		[data] => Object(
			[file_name] => file_1603916014.jpg
			[file_path] => tidewater/requests/file_1603916014.jpg
			[file_url] => https://cdn.bridge.school.nz/tidewater/requests/file_1603916014.jpg
		)
	)
```

