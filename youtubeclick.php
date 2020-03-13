<?php
defined('_JEXEC') or die;

class PlgContentYoutubeclick extends JPlugin
{
	/**
	 * Plugin that add srcdoc attribute to youtube iframe embeded videos to recuce page load time
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   object   &$article  The article object.  Note $article->text is also available
	 * @param   mixed    &$params   The article params
	 * @param   integer  $page      The 'page' number
	 *
	 * @return  mixed   true if there is an error. Void otherwise.
	 *
	 * @since   1.6
	 */
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		// Simple performance check to determine whether bot should process further
		if (strpos($article->text, 'youtube.com/embed/') === false)
		{
			return true;
		}

		// Expression to search for iframes
		$regex = '<iframe src="https:\/\/www\.youtube\.com\/embed\/.*<\/iframe>/i';
        $input_lines = $article->text;

        preg_match_all('/<iframe src="https:\/\/www\.youtube\.com\/embed\/.*<\/iframe>/i', $input_lines, $output_array);

		// No matches, skip this
		if ($output_array)
		{
			foreach ($output_array[0] as $match)
			{
				preg_match('/embed\/(.*?)"/i', $match, $matches);
				$video_id = $matches[1];
				$srcdoc = " srcdoc=\"<style>*{padding:0;margin:0;overflow:hidden}html,body{height:100%}img,span{position:absolute;width:100%;top:0;bottom:0;margin:auto}span{height:1.5em;text-align:center;font:48px/1.5 sans-serif;color:white;text-shadow:0 0 0.5em black}</style><a href=https://www.youtube.com/embed/$video_id?autoplay=1><img src=https://img.youtube.com/vi/$video_id/hqdefault.jpg><span>▶</span></a>\" ";
                $new_video_html = str_replace("<iframe ", "<iframe " . $srcdoc, $match);
				$article->text = str_replace($match, $new_video_html, $article->text);
			}
		}
	}
}
