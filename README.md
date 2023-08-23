# wp-format-enabler
Plugin to demo format power for block themes

This simple plugin is meant to explore via hacking how post formats can work 
with block themes  from two perspectives:

1. How the creation flow for the quick no title content types could work
2. How the loop could handle quick no title content types

The plugin when activated does the following, if a block theme is active:

- Enables support for post formats (all)
- Adds one admin sidebar new post link to a special editor configured for each format
- Adds a dashboatd widget which contains a
  new post link to a special editor configured for each format
- Hooks into the rendering of the post template block and renders full content if
  the currently rendered post is any format other than standard
- Customizes the post editor for format posting by presenting a default template
  per format type and hiding the title input (via CSS).
- Uses format name as title in WP admin for title-less posts

## Demo


https://github.com/draganescu/wp-format-enabler/assets/107534/02d6bda7-cf46-44da-94ff-a0c60e8f776f



