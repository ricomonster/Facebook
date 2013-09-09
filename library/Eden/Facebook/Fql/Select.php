<?php

//-->
/*
 * This file is part of the Eden package.
 * (c) 2011-2012 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Eden\Facebook\Fql;

use Eden\Facebook\Argument;
use Eden\Facebook\Base;

/**
 * Generates select query string syntax
 *
 * @vendor  Eden
 * @package Eden\Facebook\Fql
 * @author  Christian Blanquera <cblanquera@openovate.com>
 * @since   1.0.0
 */
class Select extends Base
{
    protected $select = null;
    protected $from = null;
    protected $where = array();
    protected $sortBy = array();
    protected $page = null;
    protected $length = null;

    public function __construct($select = '*')
    {
        $this->select($select);
    }

    /**
     * Returns the complete facebook query
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getQuery();
    }

    /**
     * From clause
     *
     * @param string from
     * @return Eden\Facebook\Fql\Select
     */
    public function from($from)
    {
		// argument 1 must be a string
        Argument::i()->test(1, 'string'); 

        $this->from = $from;
        return $this;
    }

    /**
     * Limit clause
     *
     * @param string|int page
     * @param string|int length
     * @return Eden\Facebook\Fql\Select
     */
    public function limit($page, $length)
    {
        Argument::i()
                ->test(1, 'numeric') // argument 1 must be a number
                ->test(2, 'numeric'); // argument 2 must be a number

        $this->page = $page;
        $this->length = $length;

        return $this;
    }

    /**
     * Returns the string version of the query 
     *
     * @param  bool
     * @return string
     */
    public function getQuery()
    {
        $where = empty($this->where) ? '' : 'WHERE ' . implode(' AND ', $this->where);
        $sort = empty($this->sortBy) ? '' : 'ORDER BY ' . implode(', ', $this->sortBy);
        $limit = is_null($this->page) ? '' : 'LIMIT ' . $this->page . ',' . $this->length;

        if (empty($this->select) || $this->select == '*') {
            $this->select = implode(', ', self::$columns[$this->from]);
        } else if ($this->select == 'COUNT(*)') {
            $this->select = self::$columns[$this->from][0];
        }

        $query = sprintf(
            'SELECT %s FROM %s %s %s %s;', $this->select, $this->from, $where, $sort, $limit);

        return str_replace('  ', ' ', $query);
    }

    /**
     * Select clause
     *
     * @param string select
     * @return Eden\Facebook\Fql\Select
     */
    public function select($select = '*')
    {
		// argument 1 must be a string or array
        Argument::i()->test(1, 'string', 'array'); 
		
        //if select is an array
        if (is_array($select)) {
            //transform into a string
            $select = implode(', ', $select);
        }

        $this->select = $select;

        return $this;
    }

    /**
     * Order by clause
     *
     * @param string field
     * @param string order
     * @return Eden\Facebook\Fql\Select
     */
    public function sortBy($field, $order = 'ASC')
    {
        Argument::i()
			->test(1, 'string') // argument 1 must be a string
			->test(2, 'string'); // argument 2 must be a string

        $this->sortBy[] = $field . ' ' . $order;

        return $this;
    }

    /**
     * Where clause
     *
     * @param array|string where
     * @return Eden\Facebook\Fql\Select
     */
    public function where($where)
    {
		// argument 1 must be a string or array
        Argument::i()->test(1, 'string', 'array'); 

        if (is_string($where)) {
            $where = array($where);
        }

        $this->where = array_merge($this->where, $where);

        return $this;
    }
	
    protected static $columns = array(
        'album' => array(
			'aid', 'backdated_time', 'can_backdate', 'can_upload', 'comment_info',
            'cover_object_id', 'cover_pid', 'created', 'description', 'edit_link',
            'is_user_facing', 'like_info', 'link', 'location', 'modified',
            'modified_major', 'name', 'object_id', 'owner', 'owner_cursor',
            'photo_count', 'place_id', 'type', 'video_count', 'visible'),
        'app_role' => array('application_id', 'developer_id', 'role'),
        'application' => array(
			'android_key_hash', 'api_key', 'app_domains', 'app_id', 'app_name',
            'app_type', 'appcenter_icon_url', 'auth_dialog_data_help_url', 
			'auth_dialog_headline', 'auth_dialog_perms_explanation',
            'auth_referral_default_activity_privacy', 'auth_referral_enabled', 
			'auth_referral_extended_perms', 'auth_referral_friend_perms', 
			'auth_referral_response_type', 'auth_referral_user_perms', 
			'canvas_fluid_height', 'canvas_fluid_width', 'canvas_url', 'category',
            'client_config', 'company_name', 'configured_ios_sso', 'contact_email', 'created_time',
            'creator_uid', 'daily_active_users', 'deauth_callback_url', 'description', 'developers',
            'display_name', 'hosting_url', 'icon_url', 'ios_bundle_id', 'ipad_app_store_id',
            'iphone_app_store_id', 'is_facebook_app', 'link', 'logo_url', 'migration_status',
            'mobile_profile_section_url', 'mobile_web_url', 'monthly_active_users', 
			'namespace', 'page_tab_default_name', 'page_tab_url', 'privacy_policy_url', 
			'profile_section_url', 'restriction_info', 'secure_canvas_url',
            'secure_page_tab_url', 'server_ip_whitelist', 'social_discovery', 'subcategory', 
			'supports_attribution', 'supports_implicit_sdk_logging', 'terms_of_service_url', 
			'url_scheme_suffix', 'user_support_email', 'user_support_url',
            'website_url', 'weekly_active_users'),
        'apprequest' => array(
			'app_id', 'created_time', 'data', 'message', 'recipient_uid',
            'request_id', 'sender_uid'),
        'checkin' => array(
			'app_id', 'author_uid', 'checkin_id', 'coords', 'message',
            'post_id', 'tagged_uids', 'target_id', 'target_type', 'timestamp'),
        'comment' => array(
			'app_id', 'attachment', 'target', 'title', 'type',
            'url', 'subattachments', 'can_comment', 'can_like', 'can_remove',
            'comment_count', 'fromid', 'id', 'is_private', 'likes',
            'object_id', 'object_id_cursor', 'parent_id', 'parent_id_cursor', 'post_fbid',
            'post_id', 'post_id_cursor', 'text', 'text_tags', 'time',
            'user_likes'),
        'comments_info' => array('app_id', 'count', 'updated_time', 'xid'),
        'connection' => array('is_following', 'source_id', 'target_id', 'target_type'),
        'cookies' => array('expires', 'name', 'path', 'uid', 'value'),
        'column' => array(
			'column_name', 'description', 'is_cursor', 'is_deprecated', 
			'table_name', 'type'),
        'developer' => array('application_id', 'developer_id', 'role'),
        'domain' => array('domain_id', 'domain_name'),
        'domain_admin' => array('domain_id', 'owner_id'),
        'event' => array(
			'all_members_count', 'attending_count', 'can_invite_friends', 'creator', 
			'creator_cursor', 'declined_count', 'description', 'eid', 'end_time', 
			'has_profile_pic', 'hide_guest_list', 'host', 'is_date_only', 'location', 
			'name', 'not_replied_count', 'parent_group_id', 'pic', 'pic_big', 'pic_cover',
            'pic_small', 'pic_square', 'privacy', 'start_time', 'ticket_uri',
            'timezone', 'unsure_count', 'update_time', 'venue', 'version'),
        'event_member' => array(
			'eid', 'inviter', 'inviter_type', 'rsvp_status', 
			'start_time', 'uid'),
        'family' => array('birthday', 'name', 'profile_id', 'relationship', 'uid'),
        'friend' => array('uid1', 'uid2'),
        'friendlist' => array(
			'count', 'flid', 'name', 'owner', 'owner_cursor', 'type'),
        'friendlist_member' => array('flid', 'flid_cursor', 'uid'),
        'friend_request' => array('is_hidden', 'message', 'time', 'uid_from', 'uid_to', 'unread'),
        'group' => array(
			'creator', 'description', 'email', 'gid', 'icon',
            'icon34', 'icon50', 'icon68', 'name', 'nid',
            'office', 'parent_id', 'pic', 'pic_big', 'pic_cover',
            'pic_small', 'pic_square', 'privacy', 'recent_news', 'update_time',
            'venue', 'website'),
        'group_member' => array('administrator', 'bookmark_order', 'gid', 'positions', 'uid',
            'unread'),
        'insights' => array('breakdown', 'end_time', 'event', 'metric', 'object_id',
            'period', 'value'),
        'like' => array('object_id', 'object_id_cursor', 'object_type', 'post_id', 'post_id_cursor',
            'user_id', 'user_id_cursor'),
        'link' => array('backdated_time', 'can_backdate', 'caption', 'comment_info', 'created_time',
            'image_urls', 'like_info', 'link_id', 'owner', 'owner_comment',
            'owner_cursor', 'picture', 'privacy', 'summary', 'title',
            'url', 'via_id'),
        'link_image_src' => array('max_height', 'max_width', 'source_url', 'url'),
        'link_stat' => array('click_count', 'comment_count', 'comments_fbid', 'commentsbox_count', 'like_count',
            'normalized_url', 'share_count', 'total_count', 'url'),
        'location_post' => array('app_id', 'author_uid', 'coords', 'id', 'latitude',
            'longitude', 'message', 'page_id', 'page_type', 'post_id',
            'tagged_uids', 'timestamp', 'type'),
        'mailbox_folder' => array('folder_id', 'name', 'total_count', 'unread_count', 'viewer_id'),
        'message' => array('attachment', 'tagged_uids', 'timestamp', 'message', 'target_type',
            'tagged_ids', 'sticker_id', 'share_id', 'type', 'title',
            'summary', 'image', 'author_id', 'body', 'created_time',
            'message_id', 'source', 'thread_id', 'viewer_id'),
        'note' => array('comment_info', 'content', 'content_html', 'created_time', 'like_info',
            'note_id', 'title', 'uid', 'uid_cursor', 'updated_time'),
        'notification' => array('app_id', 'body_html', 'body_text', 'created_time', 'href',
            'icon_url', 'is_hidden', 'is_unread', 'notification_id', 'object_id',
            'object_type', 'recipient_id', 'sender_id', 'title_html', 'title_text',
            'updated_time'),
        'object_url' => array('id', 'site', 'type', 'url'),
        'page' => array('about', 'access_token', 'affiliation', 'app_id', 'artists_we_like',
            'attire', 'awards', 'band_interests', 'band_members', 'bio',
            'birthday', 'booking_agent', 'budget_recs', 'built', 'can_post',
            'categories', 'checkins', 'company_overview', 'culinary_team', 'current_location',
            'description', 'description_html', 'directed_by', 'fan_count', 'features',
            'food_styles', 'founded', 'general_info', 'general_manager', 'genre',
            'global_brand_page_name', 'global_brand_parent_page_id', 'has_added_app', 'hometown', 'hours',
            'influences', 'is_community_page', 'is_permanently_closed', 'is_published', 'is_verified',
            'keywords', 'location', 'members', 'mission', 'mpg',
            'name', 'network', 'new_like_count', 'offer_eligible', 'page_id',
            'page_url', 'parent_page', 'parking', 'payment_options', 'personal_info',
            'personal_interests', 'pharma_safety_info', 'phone', 'pic', 'pic_big',
            'pic_cover', 'pic_large', 'pic_small', 'pic_square', 'plot_outline',
            'press_contact', 'price_range', 'produced_by', 'products', 'promotion_eligible',
            'promotion_ineligible_reason', 'public_transit', 'record_label', 'release_date', 
			'restaurant_services', 'restaurant_specialties', 'schedule', 'screenplay_by', 
			'season', 'starring', 'store_number', 'studio', 'talking_about_count', 'type', 
			'unread_message_count', 'unseen_message_count', 'unseen_notif_count', 'username', 
			'website', 'were_here_count', 'written_by'),
        'page_admin' => array('last_used_time', 'page_id', 'perms', 'role', 'type',
            'uid'),
        'page_blocked_user' => array('page_id', 'uid'),
        'page_global_brand_child' => array('global_brand_child_page_id', 'parent_page_id'),
        'page_fan' => array('created_time', 'page_id', 'profile_section', 'type', 'uid'),
        'page_milestone' => array('created_time', 'description', 'end_time', 'id', 'is_hidden',
            'owner_id', 'start_time', 'title', 'updated_time'),
        'permissions' => array(
			'ads_management', 'bookmarked', 'create_event', 'create_note', 'email',
            'export_stream', 'friends_about_me', 'friends_activities', 'friends_birthday', 
			'friends_education_history', 'friends_events', 'friends_groups', 
			'friends_hometown', 'friends_interests', 'friends_likes',
            'friends_location', 'friends_notes', 'friends_online_presence', 
			'friends_photo_video_tags', 'friends_photos',
            'friends_questions', 'friends_relationship_details', 
			'friends_relationships', 'friends_religion_politics', 'friends_status',
            'friends_subscriptions', 'friends_videos', 'friends_website', 
			'friends_work_history', 'manage_friendlists',
            'manage_notifications', 'manage_pages', 'photo_upload', 'publish_actions', 'publish_checkins',
            'publish_stream', 'read_friendlists', 'read_insights', 'read_mailbox', 'read_page_mailboxes',
            'read_requests', 'read_stream', 'rsvp_event', 'share_item', 'sms',
            'status_update', 'tab_added', 'uid', 'user_about_me', 'user_activities',
            'user_birthday', 'user_education_history', 'user_events', 'user_groups', 'user_hometown',
            'user_interests', 'user_likes', 'user_location', 'user_notes', 'user_online_presence',
            'user_photo_video_tags', 'user_photos', 'user_questions', 
			'user_relationship_details', 'user_relationships', 'user_religion_politics', 
			'user_status', 'user_subscriptions', 'user_videos', 'user_website',
            'user_work_history', 'video_upload', 'xmpp_login'),
        'permissions_info' => array('header', 'permission_name', 'summary'),
        'photo' => array('aid', 'aid_cursor', 'album_object_id', 'album_object_id_cursor', 'backdated_time',
            'backdated_time_granularity', 'can_backdate', 'can_delete', 'can_tag', 'caption',
            'caption_tags', 'comment_info', 'created', 'images', 'like_info',
            'link', 'modified', 'object_id', 'offline_id', 'owner',
            'owner_cursor', 'page_story_id', 'pid', 'place_id', 'src',
            'src_big', 'src_big_height', 'src_big_width', 'src_height', 'src_small',
            'src_small_height', 'src_small_width', 'src_width', 'target_id', 'target_type'),
        'photo_src' => array('height', 'photo_id', 'size', 'src', 'width'),
        'photo_tag' => array('created', 'object_id', 'pid', 'subject', 'text',
            'xcoord', 'ycoord'),
        'place' => array('checkin_count', 'content_age', 'description', 'display_subtext', 'geometry',
            'is_city', 'is_unclaimed', 'latitude', 'longitude', 'name',
            'page_id', 'pic', 'pic_big', 'pic_crop', 'pic_large',
            'pic_small', 'pic_square', 'search_type', 'type'),
        'privacy' => array('allow', 'deny', 'description', 'friends', 'id',
            'object_id', 'owner_id', 'value'),
        'privacy_setting' => array('allow', 'deny', 'description', 'friends', 'name',
            'value'),
        'profile' => array('can_post', 'id', 'name', 'pic', 'pic_big',
            'pic_crop', 'pic_small', 'pic_square', 'type', 'url',
            'username'),
        'profile_tab' => array('app_id', 'custom_image_url', 'custom_name', 'image_url', 'is_permanent',
            'key', 'link', 'name', 'position', 'profile_id'),
        'profile_view' => array('app_id', 'custom_image_url', 'custom_name', 'image_url', 'is_permanent',
            'key', 'link', 'name', 'position', 'profile_id'),
        'question' => array('created_time', 'id', 'is_published', 'owner', 'question',
            'updated_time'),
        'question_option' => array('created_time', 'id', 'name', 'object_id', 'owner',
            'photo_id', 'question_id', 'votes'),
        'question_option_votes' => array('option_id', 'voter_id'),
        'review' => array('created_time', 'message', 'rating', 'review_id', 'reviewee_id',
            'reviewer_id'),
        'score' => array('app_id', 'user_id', 'value'),
        'standard_friend_info' => array('uid1', 'uid2'),
        'standard_user_info' => array(
			'affiliations', 'allowed_restrictions', 'birthday', 'credit_currency', 'credit_deals',
            'current_location', 'email', 'first_name', 'last_name', 'locale',
            'name', 'payment_pricepoints', 'profile_url', 'proxied_email', 'sex',
            'sort_first_name', 'sort_last_name', 'third_party_id', 'timezone', 'uid',
            'username'),
        'status' => array('comment_info', 'like_info', 'message', 'place_id', 'source',
            'status_id', 'time', 'uid'),
        'stream' => array('action_links', 'actor_id', 'app_data', 'app_id', 'attachment',
            'tagged_uids', 'timestamp', 'message', 'target_type', 'sticker_id',
            'tagged_ids', 'attribution', 'claim_count', 'comment_info', 'created_time',
            'description', 'description_tags', 'expiration_timestamp', 'feed_targeting', 'filter_key',
            'impressions', 'is_exportable', 'is_hidden', 'is_published', 'like_info',
            'message', 'message_tags', 'parent_post_id', 'permalink', 'place',
            'post_id', 'privacy', 'promotion_status', 'scheduled_publish_time', 'share_count',
            'share_info', 'source_id', 'subscribed', 'tagged_ids', 'target_id',
            'targeting', 'timeline_visibility', 'type', 'updated_time', 'via_id',
            'viewer_id', 'with_location', 'with_tags', 'xid'),
        'stream_filter' => array('filter_key', 'icon_url', 'is_visible', 'name', 'rank',
            'type', 'uid', 'value'),
        'stream_tag' => array('actor_id', 'post_id', 'target_id'),
        'subscription' => array('subscribed_id', 'subscribed_id_cursor', 'subscriber_id', 'subscriber_id_cursor'),
        'table' => array('description', 'is_deprecated', 'name'),
        'thread' => array('folder_id', 'has_attachment', 'message_count', 'object_id', 'originator',
            'parent_message_id', 'parent_thread_id', 'recent_authors', 'recipients', 'snippet',
            'snippet_author', 'subject', 'thread_id', 'unread', 'unseen',
            'updated_time', 'viewer_id'),
        'translation' => array('approval_status', 'best_string', 'description', 'is_translatable', 'locale',
            'native_hash', 'native_string', 'pre_hash_string', 'translation', 'translation_id'),
        'unified_message' => array('action_id', 'attachment_map', 'attachments', 'body', 'containing_message_id',
            'coordinates', 'forwarded_message_id', 'forwarded_messages', 'html_body', 'is_forwarded',
            'is_user_generated', 'log_message', 'callLog', 'smsLog', 'voiceMailLog',
            'voicemailUid', 'threadName', 'threadPic', 'message_id', 'object_sender',
            'offline_threading_id', 'recipients', 'sender', 'share_map', 'shares',
            'subject', 'tags', 'thread_id', 'timestamp', 'unread'),
        'unified_message_count' => array(
			'folder', 'last_action_id', 'last_seen_time', 'refetch_action_id', 'total_threads',
            'unread_count', 'unseen_count'),
        'unified_message_sync' => array(
			'action_id', 'attachment_map', 'attachments', 'body', 'containing_message_id',
            'coordinates', 'forwarded_message_id', 'forwarded_messages', 'html_body', 'is_forwarded',
            'is_user_generated', 'log_message', 'callLog', 'smsLog', 'voiceMailLog',
            'voicemailUid', 'threadName', 'threadPic', 'message_id', 'object_sender',
            'offline_threading_id', 'recipients', 'sender', 'share_map', 'shares',
            'subject', 'sync_change_type', 'tags', 'thread_id', 'timestamp',
            'unread'),
        'unified_thread' => array('action_id', 'admin_snippet', 'archived', 'auto_mute', 'can_reply',
            'folder', 'former_participants', 'has_attachments', 'is_group_conversation', 'is_named_conversation',
            'is_subscribed', 'last_visible_add_action_id', 'link', 'mute', 'name',
            'num_messages', 'num_unread', 'object_participants', 'participants', 'pic_hash',
            'read_receipts', 'senders', 'single_recipient', 'snippet', 'snippet_message_has_attachment',
            'snippet_message_id', 'snippet_sender', 'subject', 'tags', 'thread_and_participants_name',
            'thread_fbid', 'thread_id', 'thread_participants', 'timestamp', 'title',
            'unread', 'unseen'),
        'unified_thread_action' => array(
			'action_id', 'actor', 'body', 'internal_message_id', 'thread_id',
            'timestamp', 'type', 'users'),
        'unified_thread_count' => array(
			'folder', 'last_action_id', 'last_seen_time', 'refetch_action_id', 
			'total_threads', 'unread_count', 'unseen_count'),
        'unified_thread_sync' => array('action_id', 'admin_snippet', 'archived', 'auto_mute', 'can_reply',
            'folder', 'former_participants', 'has_attachments', 'is_group_conversation', 'is_named_conversation',
            'is_subscribed', 'last_visible_add_action_id', 'link', 'mute', 'name',
            'num_messages', 'num_unread', 'object_participants', 'participants', 'pic_hash',
            'read_receipts', 'refetch_action_id', 'senders', 'single_recipient', 'snippet',
            'snippet_message_has_attachment', 'snippet_message_id', 'snippet_sender', 'subject', 
			'sync_change_type', 'tags', 'thread_and_participants_name', 'thread_fbid', 
			'thread_id', 'thread_participants', 'timestamp', 'title', 'unread', 'unseen'),
        'url_like' => array('url', 'user_id'),
        'user' => array('about_me', 'activities', 'affiliations', 'age_range', 'allowed_restrictions',
            'birthday', 'birthday_date', 'books', 'can_message', 'can_post',
            'contact_email', 'currency', 'current_address', 'current_location', 'devices',
            'education', 'email', 'email_hashes', 'first_name', 'friend_count',
            'friend_request_count', 'has_timeline', 'hometown_location', 'inspirational_people', 'install_type',
            'interests', 'is_app_user', 'is_blocked', 'is_verified', 'languages',
            'last_name', 'likes_count', 'locale', 'meeting_for', 'meeting_sex',
            'middle_name', 'movies', 'music', 'mutual_friend_count', 'name',
            'name_format', 'notes_count', 'online_presence', 'payment_instruments', 'payment_pricepoints',
            'pic', 'pic_big', 'pic_big_with_logo', 'pic_cover', 'pic_small',
            'pic_small_with_logo', 'pic_square', 'pic_square_with_logo', 'pic_with_logo', 'political',
            'profile_blurb', 'profile_update_time', 'profile_url', 'proxied_email', 'quotes',
            'relationship_status', 'religion', 'search_tokens', 'security_settings', 'sex',
            'shipping_information', 'significant_other_id', 'sort_first_name', 'sort_last_name', 'sports',
            'status', 'subscriber_count', 'third_party_id', 'timezone', 'tv',
            'uid', 'username', 'verified', 'video_upload_limits', 'viewer_can_send_gift',
            'wall_count', 'website', 'work'),
        'video' => array('album_id', 'created_time', 'description', 'embed_html', 'format',
            'length', 'link', 'owner', 'src', 'src_hq',
            'thumbnail_link', 'title', 'updated_time', 'vid'),
        'video_tag' => array('created_time', 'subject', 'updated_time', 'vid'));
}