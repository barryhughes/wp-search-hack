# WP Search Hack

_"The kind of WP native search enhancement your mother warned you to stay away from."_ 

* When a post is updated, all public taxonomy terms for that post will be inserted within an HTML comment 
* WP can now locate those posts based on a search using those terms!
* When the post is updated, the comment is removed and rebuilt
* Before the post is displayed, the comment is removed

Cheap, dirty, unsupported and unsuitable for serious use.

### Notes

* The `ImprovedSearchHack.hidden_terms` filter hook provides an opportunity to modify the extra search terms for each post
* It doesn't work retroactively, ie it will not magically add these comments to older posts - manual updating of old posts is required
* Assumes a moderately up-to-date PHP and WordPress environment