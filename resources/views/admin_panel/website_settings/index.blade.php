@extends('admin_panel.layout.app')

@section('content')
<style>
    :root {
        --ws-primary: #e11d48;
        --ws-primary-hover: #be123c;
        --ws-gold: #f59e0b;
        --ws-bg: #f8fafc;
        --ws-card: #ffffff;
        --ws-border: #e2e8f0;
        --ws-text: #0f172a;
        --ws-muted: #64748b;
    }

    .ws-container {
        padding: 24px 0;
        background: var(--ws-bg);
        min-height: 100vh;
    }

    .ws-header {
        background: white;
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 24px;
        border: 1px solid var(--ws-border);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .ws-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--ws-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ws-title i {
        color: var(--ws-primary);
    }

    .ws-subtitle {
        color: var(--ws-muted);
        font-size: 0.9rem;
        margin-top: 4px;
    }

    /* Tabs Styling */
    .ws-tabs-wrapper {
        background: white;
        border-radius: 14px;
        padding: 8px;
        margin-bottom: 24px;
        border: 1px solid var(--ws-border);
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .ws-tabs-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .ws-tab-btn {
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--ws-muted);
        border: none;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ws-tab-btn:hover {
        background: #f1f5f9;
        color: var(--ws-text);
    }

    .ws-tab-btn.active {
        background: linear-gradient(135deg, #e11d48, #be123c);
        color: white;
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.25);
    }

    .ws-tab-btn.active i {
        color: white !important;
    }

    /* Card & Section styling */
    .ws-card {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--ws-border);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .ws-card-header {
        padding: 20px 24px;
        background: #fafafa;
        border-bottom: 1px solid var(--ws-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ws-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--ws-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ws-card-body {
        padding: 28px 24px;
    }

    /* Form styling */
    .form-group-custom {
        margin-bottom: 22px;
    }

    .form-label-custom {
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .form-control-custom {
        width: 100%;
        padding: 11px 16px;
        font-size: 0.95rem;
        color: var(--ws-text);
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        transition: all 0.2s;
    }

    .form-control-custom:focus {
        border-color: var(--ws-primary);
        box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.12);
        outline: none;
    }

    .form-hint {
        font-size: 0.78rem;
        color: var(--ws-muted);
        margin-top: 6px;
    }

    /* Image Preview Box */
    .img-preview-box {
        width: 100%;
        max-width: 240px;
        height: 150px;
        border-radius: 12px;
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-top: 10px;
        position: relative;
    }

    .img-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .img-preview-box.logo-preview {
        max-width: 160px;
        height: 80px;
        background: #1e293b;
    }

    .img-preview-box.logo-preview img {
        object-fit: contain;
        padding: 8px;
    }

    /* Feature & Review Mini Cards */
    .feature-edit-card, .review-edit-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px;
        height: 100%;
    }

    .feature-card-header, .review-card-header {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--ws-text);
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 10px;
    }

    /* Sticky Save Footer */
    .ws-save-bar {
        position: sticky;
        bottom: 20px;
        background: white;
        border-radius: 14px;
        padding: 16px 24px;
        border: 1px solid var(--ws-border);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 100;
        margin-top: 20px;
    }

    .btn-save-main {
        background: linear-gradient(135deg, #e11d48, #be123c);
        color: white;
        font-weight: 700;
        padding: 12px 28px;
        border-radius: 10px;
        border: none;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.95rem;
        box-shadow: 0 4px 15px rgba(225, 29, 72, 0.3);
        transition: all 0.2s;
    }

    .btn-save-main:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(225, 29, 72, 0.4);
        color: white;
    }

    .btn-view-site {
        background: #f1f5f9;
        color: #334155;
        font-weight: 600;
        padding: 12px 22px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-view-site:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
</style>

<div class="ws-container">
    <div class="container-fluid px-4">
        
        <!-- Page Header -->
        <div class="ws-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="ws-title">
                    <i class="fa fa-sliders"></i>
                    Website Control &amp; Content Manager
                </h1>
                <p class="ws-subtitle">Manage all live website text, images, announcements, hero section, deals, and reviews in one place.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('website') }}" target="_blank" class="btn-view-site">
                    <i class="fa fa-external-link-alt text-primary"></i>
                    <span>Live Website Preview</span>
                </a>
                <button type="button" onclick="$('#websiteSettingsForm').submit();" class="btn-save-main">
                    <i class="fa fa-save"></i>
                    <span>Save All Changes</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm d-flex align-items-center gap-2" role="alert">
                <i class="fa fa-check-circle fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form id="websiteSettingsForm" action="{{ route('website_settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Navigation Tabs -->
            <div class="ws-tabs-wrapper">
                <ul class="ws-tabs-nav" id="wsTabs">
                    <li>
                        <button type="button" class="ws-tab-btn active" data-tab="tab-branding">
                            <i class="fa fa-building text-danger"></i> Header &amp; Branding
                        </button>
                    </li>
                    <li>
                        <button type="button" class="ws-tab-btn" data-tab="tab-hero">
                            <i class="fa fa-bolt text-warning"></i> Hero &amp; Main Banner
                        </button>
                    </li>
                    <li>
                        <button type="button" class="ws-tab-btn" data-tab="tab-sections">
                            <i class="fa fa-utensils text-success"></i> Menu &amp; Deals Titles
                        </button>
                    </li>
                    <li>
                        <button type="button" class="ws-tab-btn" data-tab="tab-features">
                            <i class="fa fa-star text-info"></i> Why Choose Us
                        </button>
                    </li>
                    <li>
                        <button type="button" class="ws-tab-btn" data-tab="tab-reviews">
                            <i class="fa fa-comments text-primary"></i> Customer Reviews
                        </button>
                    </li>
                    <li>
                        <button type="button" class="ws-tab-btn" data-tab="tab-footer">
                            <i class="fa fa-globe text-secondary"></i> Footer &amp; Social Links
                        </button>
                    </li>
                </ul>
            </div>

            <!-- TAB 1: Header & Branding -->
            <div class="ws-tab-content active" id="tab-branding">
                <div class="ws-card">
                    <div class="ws-card-header">
                        <h3 class="ws-card-title"><i class="fa fa-building text-danger"></i> Restaurant Identity &amp; Top Banner</h3>
                    </div>
                    <div class="ws-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-store text-danger"></i> Restaurant / Brand Name</label>
                                    <input type="text" name="site_name" class="form-control-custom" value="{{ $settings['site_name'] ?? 'Munch Fried Chicken' }}" required>
                                    <div class="form-hint">Displayed in the logo area, page title, and receipts.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-quote-left text-warning"></i> Brand Tagline</label>
                                    <input type="text" name="site_tagline" class="form-control-custom" value="{{ $settings['site_tagline'] ?? 'Handcrafted • Secret 12-Spice Blend' }}">
                                    <div class="form-hint">Shown in browser header and promotional sub-banners.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-bullhorn text-danger"></i> Top Announcement Banner</label>
                                    <input type="text" name="top_announcement" class="form-control-custom" value="{{ $settings['top_announcement'] ?? 'FREE DELIVERY on all orders above Rs. 1,500!' }}">
                                    <div class="form-hint">Top ticker message (e.g. Free delivery on orders over Rs. 1,500).</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-clock text-info"></i> Opening Hours</label>
                                    <input type="text" name="opening_hours" class="form-control-custom" value="{{ $settings['opening_hours'] ?? '12:00 PM – 02:00 AM Daily' }}">
                                    <div class="form-hint">Restaurant operational timing for customers.</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-phone text-success"></i> Phone Number</label>
                                    <input type="text" name="contact_phone" class="form-control-custom" value="{{ $settings['contact_phone'] ?? '0300-1234567' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fab fa-whatsapp text-success"></i> WhatsApp Order Number</label>
                                    <input type="text" name="whatsapp_number" class="form-control-custom" value="{{ $settings['whatsapp_number'] ?? '923001234567' }}" placeholder="92300xxxxxxx">
                                    <div class="form-hint">With country code (e.g. 923001234567) for WhatsApp order redirection.</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-motorcycle text-warning"></i> Standard Delivery Fee (Rs.)</label>
                                    <input type="number" name="delivery_charges" class="form-control-custom" value="{{ $settings['delivery_charges'] ?? '150' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-gift text-danger"></i> Free Delivery Minimum Order (Rs.)</label>
                                    <input type="number" name="free_delivery_above" class="form-control-custom" value="{{ $settings['free_delivery_above'] ?? '1500' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-map-marker-alt text-danger"></i> Restaurant Address</label>
                                    <input type="text" name="contact_address" class="form-control-custom" value="{{ $settings['contact_address'] ?? 'Main Commercial Boulevard, Food Street, Hyderabad' }}">
                                </div>
                            </div>

                            <!-- Logo Upload -->
                            <div class="col-md-12">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-image text-primary"></i> Custom Website Logo Image</label>
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <input type="file" name="site_logo_file" id="logoFileInput" class="form-control-custom" accept="image/*">
                                            <input type="hidden" name="site_logo" value="{{ $settings['site_logo'] ?? '' }}">
                                            <div class="form-hint">Recommended format: PNG transparent, around 200x80 pixels. Leave empty to use default.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="img-preview-box logo-preview" id="logoPreviewWrap">
                                                @if(!empty($settings['site_logo']))
                                                    <img src="{{ $settings['site_logo'] }}" id="logoPreviewImg" alt="Logo">
                                                @else
                                                    <span class="text-white-50 small">🍗 Default Icon</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Hero Section -->
            <div class="ws-tab-content d-none" id="tab-hero">
                <div class="ws-card">
                    <div class="ws-card-header">
                        <h3 class="ws-card-title"><i class="fa fa-bolt text-warning"></i> Hero Banner &amp; Main Headline</h3>
                    </div>
                    <div class="ws-card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-award text-warning"></i> Top Hero Badge Text</label>
                                    <input type="text" name="hero_badge" class="form-control-custom" value="{{ $settings['hero_badge'] ?? 'Handcrafted • Secret 12-Spice Blend' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-heading"></i> Headline Line 1</label>
                                    <input type="text" name="hero_title_line1" class="form-control-custom" value="{{ $settings['hero_title_line1'] ?? 'CRUNCHY.' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-tint text-warning"></i> Line 2 (Gold Gradient Highlight)</label>
                                    <input type="text" name="hero_title_gold" class="form-control-custom" value="{{ $settings['hero_title_gold'] ?? 'JUICY.' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-fire text-danger"></i> Line 3 (Red Highlight)</label>
                                    <input type="text" name="hero_title_red" class="form-control-custom" value="{{ $settings['hero_title_red'] ?? 'UNFORGETTABLE.' }}">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-paragraph"></i> Hero Lead Paragraph / Description</label>
                                    <textarea name="hero_lead" rows="3" class="form-control-custom">{{ $settings['hero_lead'] ?? 'Indulge in 100% fresh, farm-raised Halal chicken hand-breaded in our proprietary golden recipe. Fried to sizzling crunch outside, bursting with tender flavors inside.' }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-mouse-pointer text-danger"></i> Button 1 Text (Order Now)</label>
                                    <input type="text" name="hero_btn1_text" class="form-control-custom" value="{{ $settings['hero_btn1_text'] ?? 'Order Online Now' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fab fa-whatsapp text-success"></i> Button 2 Text (WhatsApp)</label>
                                    <input type="text" name="hero_btn2_text" class="form-control-custom" value="{{ $settings['hero_btn2_text'] ?? 'WhatsApp Order' }}">
                                </div>
                            </div>

                            <!-- Hero Main Image -->
                            <div class="col-md-12 mt-2">
                                <div class="p-3 rounded-3 border bg-light mb-4">
                                    <label class="form-label-custom"><i class="fa fa-image text-danger"></i> Hero Floating Main Dish Image</label>
                                    <div class="row align-items-center">
                                        <div class="col-md-7">
                                            <label class="small text-muted mb-1">Upload New Image File:</label>
                                            <input type="file" name="hero_image_file" id="heroFileInput" class="form-control-custom mb-2" accept="image/*">
                                            <label class="small text-muted mb-1">Or Direct Web Image URL:</label>
                                            <input type="text" name="hero_image" id="heroImageUrlInput" class="form-control-custom" value="{{ $settings['hero_image'] ?? '' }}">
                                            <div class="form-hint">Image floats on the right of the hero banner. High-resolution dish photo recommended.</div>
                                        </div>
                                        <div class="col-md-5 text-center">
                                            <div class="img-preview-box mx-auto" id="heroPreviewWrap">
                                                <img src="{{ $settings['hero_image'] ?? 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=1000&q=85' }}" id="heroPreviewImg" alt="Hero Dish">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hero Floating Deal Badge -->
                            <div class="col-md-12">
                                <h5 class="fw-bold text-dark mb-3"><i class="fa fa-tag text-warning"></i> Floating Deal Badge On Hero Image</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group-custom">
                                    <label class="form-label-custom">Badge Emoji / Icon</label>
                                    <input type="text" name="hero_deal_emoji" class="form-control-custom" value="{{ $settings['hero_deal_emoji'] ?? '🔥' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group-custom">
                                    <label class="form-label-custom">Deal Tag / Pill</label>
                                    <input type="text" name="hero_deal_tag" class="form-control-custom" value="{{ $settings['hero_deal_tag'] ?? 'Mega Deal' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group-custom">
                                    <label class="form-label-custom">Deal Title</label>
                                    <input type="text" name="hero_deal_title" class="form-control-custom" value="{{ $settings['hero_deal_title'] ?? 'Family Bucket Feast' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group-custom">
                                    <label class="form-label-custom">Deal Price Text</label>
                                    <input type="text" name="hero_deal_price" class="form-control-custom" value="{{ $settings['hero_deal_price'] ?? 'Starting from Rs. 1,050' }}">
                                </div>
                            </div>

                            <!-- 3 Quick Stats Counter -->
                            <div class="col-md-12 mt-2">
                                <h5 class="fw-bold text-dark mb-3"><i class="fa fa-chart-line text-info"></i> Hero Quick Stats Strip</h5>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light">
                                    <label class="form-label-custom">Stat 1 Value</label>
                                    <input type="text" name="hero_stat1_val" class="form-control-custom mb-2" value="{{ $settings['hero_stat1_val'] ?? '4.9 ★' }}">
                                    <label class="form-label-custom">Stat 1 Caption</label>
                                    <input type="text" name="hero_stat1_text" class="form-control-custom" value="{{ $settings['hero_stat1_text'] ?? '2,800+ Foodies' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light">
                                    <label class="form-label-custom">Stat 2 Value</label>
                                    <input type="text" name="hero_stat2_val" class="form-control-custom mb-2" value="{{ $settings['hero_stat2_val'] ?? '30 Min' }}">
                                    <label class="form-label-custom">Stat 2 Caption</label>
                                    <input type="text" name="hero_stat2_text" class="form-control-custom" value="{{ $settings['hero_stat2_text'] ?? 'Fast Hot Delivery' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light">
                                    <label class="form-label-custom">Stat 3 Value</label>
                                    <input type="text" name="hero_stat3_val" class="form-control-custom mb-2" value="{{ $settings['hero_stat3_val'] ?? '100%' }}">
                                    <label class="form-label-custom">Stat 3 Caption</label>
                                    <input type="text" name="hero_stat3_text" class="form-control-custom" value="{{ $settings['hero_stat3_text'] ?? 'Halal & Fresh Daily' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: Deals & Menu Titles -->
            <div class="ws-tab-content d-none" id="tab-sections">
                <div class="ws-card">
                    <div class="ws-card-header">
                        <h3 class="ws-card-title"><i class="fa fa-utensils text-success"></i> Deals &amp; Menu Section Headings</h3>
                    </div>
                    <div class="ws-card-body">
                        <!-- Deals Section Headings -->
                        <div class="p-3 rounded border mb-4" style="background:#fffaf0;">
                            <h5 class="fw-bold text-warning mb-3"><i class="fa fa-tags me-1"></i> "Today's Hot Deals" Section Headings</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Deals Top Tag / Pill</label>
                                        <input type="text" name="deals_tag" class="form-control-custom" value="{{ $settings['deals_tag'] ?? "Chef's Signature Specials" }}">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Deals Section Main Title</label>
                                        <input type="text" name="deals_title" class="form-control-custom" value="{{ $settings['deals_title'] ?? "Today's Hot Deals & Combos" }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group-custom mb-0">
                                        <label class="form-label-custom">Deals Subtitle Description</label>
                                        <textarea name="deals_subtitle" rows="2" class="form-control-custom">{{ $settings['deals_subtitle'] ?? 'Specially curated value buckets and mouthwatering combos designed for maximum crunch and big savings.' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Section Headings -->
                        <div class="p-3 rounded border" style="background:#f0fdf4;">
                            <h5 class="fw-bold text-success mb-3"><i class="fa fa-list me-1"></i> "Explore Our Menu" Section Headings</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Menu Top Tag / Pill</label>
                                        <input type="text" name="menu_tag" class="form-control-custom" value="{{ $settings['menu_tag'] ?? 'Fresh From The Kitchen' }}">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Menu Section Main Title</label>
                                        <input type="text" name="menu_title" class="form-control-custom" value="{{ $settings['menu_title'] ?? 'Explore Our Full Menu' }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group-custom mb-0">
                                        <label class="form-label-custom">Menu Subtitle Description</label>
                                        <textarea name="menu_subtitle" rows="2" class="form-control-custom">{{ $settings['menu_subtitle'] ?? 'Every piece is fresh, marinated with authentic seasonings, and fried to golden perfection on order.' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: Why Choose Us (4 Cards) -->
            <div class="ws-tab-content d-none" id="tab-features">
                <div class="ws-card">
                    <div class="ws-card-header">
                        <h3 class="ws-card-title"><i class="fa fa-star text-info"></i> "Why Choose Us" Features (4 Highlight Cards)</h3>
                    </div>
                    <div class="ws-card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label class="form-label-custom">Section Tag</label>
                                    <input type="text" name="why_tag" class="form-control-custom" value="{{ $settings['why_tag'] ?? 'Quality In Every Bite' }}">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group-custom">
                                    <label class="form-label-custom">Section Main Title</label>
                                    <input type="text" name="why_title" class="form-control-custom" value="{{ $settings['why_title'] ?? 'Why Foodies Love Munch' }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group-custom">
                                    <label class="form-label-custom">Section Subtitle</label>
                                    <input type="text" name="why_subtitle" class="form-control-custom" value="{{ $settings['why_subtitle'] ?? 'We never compromise on freshness, hygiene, or authentic crunch. Here is what makes us extraordinary.' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Card 1 -->
                            <div class="col-md-6 col-lg-3">
                                <div class="feature-edit-card">
                                    <div class="feature-card-header"><i class="fa fa-check text-success"></i> Feature Card 1</div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Emoji / Icon:</label>
                                        <input type="text" name="why_icon_1" class="form-control-custom" value="{{ $settings['why_icon_1'] ?? '🍗' }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Card Title:</label>
                                        <input type="text" name="why_title_1" class="form-control-custom" value="{{ $settings['why_title_1'] ?? '100% Fresh Halal' }}">
                                    </div>
                                    <div class="form-group-custom mb-0">
                                        <label class="small text-muted fw-bold">Description:</label>
                                        <textarea name="why_desc_1" rows="3" class="form-control-custom">{{ $settings['why_desc_1'] ?? 'Never frozen chicken sourced daily from certified farms. Tender, juicy, and naturally delicious.' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2 -->
                            <div class="col-md-6 col-lg-3">
                                <div class="feature-edit-card">
                                    <div class="feature-card-header"><i class="fa fa-check text-success"></i> Feature Card 2</div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Emoji / Icon:</label>
                                        <input type="text" name="why_icon_2" class="form-control-custom" value="{{ $settings['why_icon_2'] ?? '🌶️' }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Card Title:</label>
                                        <input type="text" name="why_title_2" class="form-control-custom" value="{{ $settings['why_title_2'] ?? 'Secret 12-Spice Blend' }}">
                                    </div>
                                    <div class="form-group-custom mb-0">
                                        <label class="small text-muted fw-bold">Description:</label>
                                        <textarea name="why_desc_2" rows="3" class="form-control-custom">{{ $settings['why_desc_2'] ?? 'A proprietary master mix of aromatic spices marinated for 12 hours for deep, explosive flavor.' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 3 -->
                            <div class="col-md-6 col-lg-3">
                                <div class="feature-edit-card">
                                    <div class="feature-card-header"><i class="fa fa-check text-success"></i> Feature Card 3</div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Emoji / Icon:</label>
                                        <input type="text" name="why_icon_3" class="form-control-custom" value="{{ $settings['why_icon_3'] ?? '⚡' }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Card Title:</label>
                                        <input type="text" name="why_title_3" class="form-control-custom" value="{{ $settings['why_title_3'] ?? 'Super Fast Delivery' }}">
                                    </div>
                                    <div class="form-group-custom mb-0">
                                        <label class="small text-muted fw-bold">Description:</label>
                                        <textarea name="why_desc_3" rows="3" class="form-control-custom">{{ $settings['why_desc_3'] ?? 'Piping hot food delivered straight to your doorstep in thermal heat-sealed boxes within 30-40 mins.' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 4 -->
                            <div class="col-md-6 col-lg-3">
                                <div class="feature-edit-card">
                                    <div class="feature-card-header"><i class="fa fa-check text-success"></i> Feature Card 4</div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Emoji / Icon:</label>
                                        <input type="text" name="why_icon_4" class="form-control-custom" value="{{ $settings['why_icon_4'] ?? '🧼' }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Card Title:</label>
                                        <input type="text" name="why_title_4" class="form-control-custom" value="{{ $settings['why_title_4'] ?? 'Ultra Clean Kitchens' }}">
                                    </div>
                                    <div class="form-group-custom mb-0">
                                        <label class="small text-muted fw-bold">Description:</label>
                                        <textarea name="why_desc_4" rows="3" class="form-control-custom">{{ $settings['why_desc_4'] ?? 'Prepared under stringent food safety and hygienic protocols in modern commercial deep-fryers.' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: Customer Reviews (3 Cards) -->
            <div class="ws-tab-content d-none" id="tab-reviews">
                <div class="ws-card">
                    <div class="ws-card-header">
                        <h3 class="ws-card-title"><i class="fa fa-comments text-primary"></i> Customer Reviews &amp; Testimonials</h3>
                    </div>
                    <div class="ws-card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label class="form-label-custom">Reviews Section Tag</label>
                                    <input type="text" name="reviews_tag" class="form-control-custom" value="{{ $settings['reviews_tag'] ?? 'Real Foodie Experiences' }}">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group-custom">
                                    <label class="form-label-custom">Reviews Main Title</label>
                                    <input type="text" name="reviews_title" class="form-control-custom" value="{{ $settings['reviews_title'] ?? 'Loved by Thousands' }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group-custom">
                                    <label class="form-label-custom">Reviews Subtitle</label>
                                    <input type="text" name="reviews_subtitle" class="form-control-custom" value="{{ $settings['reviews_subtitle'] ?? 'Discover what our happy diners and online orderers say about Munch Fried Chicken.' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- Review 1 -->
                            <div class="col-md-4">
                                <div class="review-edit-card">
                                    <div class="review-card-header"><i class="fa fa-user-circle text-warning"></i> Review 1</div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Customer Name:</label>
                                        <input type="text" name="rev_name_1" class="form-control-custom" value="{{ $settings['rev_name_1'] ?? 'Ali Zafar' }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Customer Badge / Role:</label>
                                        <input type="text" name="rev_role_1" class="form-control-custom" value="{{ $settings['rev_role_1'] ?? 'Verified Foodie • 15+ Orders' }}">
                                    </div>
                                    <div class="form-group-custom mb-0">
                                        <label class="small text-muted fw-bold">Review Quote:</label>
                                        <textarea name="rev_text_1" rows="4" class="form-control-custom">{{ $settings['rev_text_1'] ?? "Hands down the crispiest fried chicken I've tasted in town! The seasoning hits every note and the Double Crunch Tower Burger is colossal. Fast delivery too!" }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Review 2 -->
                            <div class="col-md-4">
                                <div class="review-edit-card">
                                    <div class="review-card-header"><i class="fa fa-user-circle text-warning"></i> Review 2</div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Customer Name:</label>
                                        <input type="text" name="rev_name_2" class="form-control-custom" value="{{ $settings['rev_name_2'] ?? 'Sana Naveed' }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Customer Badge / Role:</label>
                                        <input type="text" name="rev_role_2" class="form-control-custom" value="{{ $settings['rev_role_2'] ?? 'Regular Family Customer' }}">
                                    </div>
                                    <div class="form-group-custom mb-0">
                                        <label class="small text-muted fw-bold">Review Quote:</label>
                                        <textarea name="rev_text_2" rows="4" class="form-control-custom">{{ $settings['rev_text_2'] ?? "The Family Feast bucket was piping hot when it arrived! The pieces are generous, skin is insanely crunchy without feeling oily. Our kids loved the wings!" }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Review 3 -->
                            <div class="col-md-4">
                                <div class="review-edit-card">
                                    <div class="review-card-header"><i class="fa fa-user-circle text-warning"></i> Review 3</div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Customer Name:</label>
                                        <input type="text" name="rev_name_3" class="form-control-custom" value="{{ $settings['rev_name_3'] ?? 'Hamza Khan' }}">
                                    </div>
                                    <div class="form-group-custom">
                                        <label class="small text-muted fw-bold">Customer Badge / Role:</label>
                                        <input type="text" name="rev_role_3" class="form-control-custom" value="{{ $settings['rev_role_3'] ?? 'Food Blogger' }}">
                                    </div>
                                    <div class="form-group-custom mb-0">
                                        <label class="small text-muted fw-bold">Review Quote:</label>
                                        <textarea name="rev_text_3" rows="4" class="form-control-custom">{{ $settings['rev_text_3'] ?? "Ordered online and received instant WhatsApp updates. The loaded fries with melted cheese and chicken bites are addictive. 10/10 highly recommended!" }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: Footer & Social Links -->
            <div class="ws-tab-content d-none" id="tab-footer">
                <div class="ws-card">
                    <div class="ws-card-header">
                        <h3 class="ws-card-title"><i class="fa fa-globe text-secondary"></i> Footer, Social Handles &amp; Map</h3>
                    </div>
                    <div class="ws-card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-align-left text-primary"></i> Footer About Paragraph</label>
                                    <textarea name="footer_about" rows="3" class="form-control-custom">{{ $settings['footer_about'] ?? "Crafting Hyderabad's favorite crispy fried chicken, towering burgers, and sizzling deals. Freshly prepared, hygienically packed, and delivered blazing fast." }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fab fa-facebook text-primary"></i> Facebook Page URL</label>
                                    <input type="text" name="facebook_url" class="form-control-custom" value="{{ $settings['facebook_url'] ?? 'https://facebook.com' }}" placeholder="https://facebook.com/yourpage">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fab fa-instagram text-danger"></i> Instagram Profile URL</label>
                                    <input type="text" name="instagram_url" class="form-control-custom" value="{{ $settings['instagram_url'] ?? 'https://instagram.com' }}" placeholder="https://instagram.com/yourprofile">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fab fa-tiktok text-dark"></i> TikTok Profile URL</label>
                                    <input type="text" name="tiktok_url" class="form-control-custom" value="{{ $settings['tiktok_url'] ?? 'https://tiktok.com' }}" placeholder="https://tiktok.com/@yourhandle">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group-custom">
                                    <label class="form-label-custom"><i class="fa fa-copyright text-muted"></i> Footer Copyright Text</label>
                                    <input type="text" name="copyright_text" class="form-control-custom" value="{{ $settings['copyright_text'] ?? '© 2026 MUNCH FRIED CHICKEN. All Rights Reserved.' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Bottom Bar -->
            <div class="ws-save-bar">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-success py-2 px-3 fs-6"><i class="fa fa-circle me-1" style="font-size: 8px;"></i> System Ready</span>
                    <span class="text-muted small d-none d-md-inline">Any change saved here will update the live website immediately.</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('website') }}" target="_blank" class="btn-view-site">
                        <i class="fa fa-eye"></i> View Live Site
                    </a>
                    <button type="submit" class="btn-save-main">
                        <i class="fa fa-check-circle"></i> Save All Changes
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Tabs Switching
        $('.ws-tab-btn').click(function() {
            var targetTab = $(this).data('tab');
            $('.ws-tab-btn').removeClass('active');
            $(this).addClass('active');

            $('.ws-tab-content').addClass('d-none').removeClass('active');
            $('#' + targetTab).removeClass('d-none').addClass('active');
        });

        // Logo Image Preview
        $('#logoFileInput').change(function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(evt) {
                    $('#logoPreviewWrap').html('<img src="' + evt.target.result + '" id="logoPreviewImg" alt="Logo">');
                }
                reader.readAsDataURL(file);
            }
        });

        // Hero Image Preview
        $('#heroFileInput').change(function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(evt) {
                    $('#heroPreviewImg').attr('src', evt.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        $('#heroImageUrlInput').on('input', function() {
            var val = $(this).val().trim();
            if (val) {
                $('#heroPreviewImg').attr('src', val);
            }
        });
    });
</script>
@endsection
