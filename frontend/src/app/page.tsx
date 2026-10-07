"use client";

import { useQuery } from "@tanstack/react-query";
import { useEffect, useRef, useState } from "react";
import api from "@/lib/api";
import { Product, Category } from "@/types";
import ProductCard from "@/components/ProductCard";
import Link from "next/link";
import { ArrowRight, Star, ChevronLeft, ChevronRight } from "lucide-react";
import { motion, useScroll, useTransform } from "framer-motion";
import { useSettings } from "@/hooks/useSettings";
import { getProductFallbackImage, getAssetUrl } from "@/lib/imageHelper";



export default function Home() {
  const { data: settings } = useSettings();
  const sliderRef = useRef<HTMLDivElement>(null);
  const collectionSliderRef = useRef<HTMLDivElement>(null);
  const categoryPillsRef = useRef<HTMLDivElement>(null);
  const videoRef = useRef<HTMLVideoElement>(null);
  const [videoError, setVideoError] = useState(false);
  const [activeCollectionTab, setActiveCollectionTab] = useState<string>("ALL");

  const scrollCollectionProducts = (direction: "left" | "right") => {
    if (collectionSliderRef.current) {
      const container = collectionSliderRef.current;
      const scrollAmount = direction === "left" ? -320 : 320;
      container.scrollBy({ left: scrollAmount, behavior: "smooth" });
    }
  };

  const scrollCategoryPills = (direction: "left" | "right") => {
    if (categoryPillsRef.current) {
      const scrollAmount = direction === "left" ? -260 : 260;
      categoryPillsRef.current.scrollBy({ left: scrollAmount, behavior: "smooth" });
    }
  };

  // Parallax Scroll Transformations
  const { scrollY } = useScroll();
  const heroParallaxY = useTransform(scrollY, [0, 800], [0, 180]);
  const heroMediaScale = useTransform(scrollY, [0, 800], [1, 1.1]);
  const newArrivalsParallaxY = useTransform(scrollY, [1200, 2500], [-30, 30]);
  const storeParallaxY = useTransform(scrollY, [2000, 4000], [-50, 50]);

  // Pause heavy background video when scrolled down to save mobile memory/GPU
  useEffect(() => {
    const handleVideoPauseOnScroll = () => {
      if (videoRef.current) {
        if (window.scrollY > window.innerHeight) {
          if (!videoRef.current.paused) {
            videoRef.current.pause();
          }
        } else {
          if (videoRef.current.paused) {
            videoRef.current.play().catch(() => {});
          }
        }
      }
    };
    window.addEventListener("scroll", handleVideoPauseOnScroll, { passive: true });
    return () => window.removeEventListener("scroll", handleVideoPauseOnScroll);
  }, []);

  const scrollCategories = (direction: "left" | "right") => {
    if (sliderRef.current) {
      const container = sliderRef.current;
      const firstCard = container.querySelector('a');
      if (firstCard) {
        const cardWidth = firstCard.clientWidth;
        let cardsToScroll = 1;
        if (window.innerWidth >= 1024) {
          cardsToScroll = 3;
        } else if (window.innerWidth >= 640) {
          cardsToScroll = 2;
        }
        
        const gap = 10;
        const scrollAmount = (cardWidth + gap) * cardsToScroll;
        container.scrollBy({
          left: direction === "left" ? -scrollAmount : scrollAmount,
          behavior: "smooth"
        });
      } else {
        const containerWidth = container.clientWidth;
        const scrollAmount = direction === "left" ? -containerWidth : containerWidth;
        container.scrollBy({ left: scrollAmount, behavior: "smooth" });
      }
    }
  };

  // Fetch products for different sections
  const { data: homeProducts, isLoading: loadingProducts } = useQuery({
    queryKey: ["home-products"],
    queryFn: async () => {
      const res = await api.get("/products");
      return res.data?.data?.data as Product[] || [];
    },
  });

  const { data: rawCategories } = useQuery({
    queryKey: ["home-categories-v5"],
    queryFn: async () => {
      const res = await api.get("/categories");
      return res.data?.data as Category[] || [];
    },
    staleTime: 0,
    refetchOnMount: true,
  });

  // Purely dynamic categories fetched from Website Settings API (/api/categories)
  const categories: Category[] = (() => {
    const fetched = rawCategories || [];
    if (!fetched.some(c => c && c.name && c.name.toUpperCase() === "ALL")) {
      return [{ id: 0, name: "ALL" }, ...fetched];
    }
    return fetched;
  })();

  // 100% Pure Dynamic products fetched strictly from backend database
  const effectiveHomeProducts: Product[] = homeProducts || [];

  // Dispatch event when home products are fetched
  useEffect(() => {
    if (!loadingProducts && homeProducts) {
      (window as any).__productsLoaded = true;
      window.dispatchEvent(new Event("products-loaded"));
    }
  }, [loadingProducts, homeProducts]);

  // Set default active tab once categories are loaded
  useEffect(() => {
    if (categories && categories.length > 0) {
      setActiveCollectionTab((prev) => {
        if (!prev || !categories.some((cat) => cat.name === prev)) {
          return categories[0].name;
        }
        return prev;
      });
    } else {
      setActiveCollectionTab("ALL");
    }
  }, [categories]);

  // Filter products by tags
  const rawNewArrivals = effectiveHomeProducts.filter(p => p.promo_tag === "New Arrival");
  const newArrivals = rawNewArrivals.length > 0 ? rawNewArrivals : effectiveHomeProducts.slice(0, 6);

  const rawTrending = effectiveHomeProducts.filter(p => p.promo_tag === "Trending" || p.promo_tag === "Best Seller");
  const trendingProducts = rawTrending.length > 0 ? rawTrending : effectiveHomeProducts.slice(2, 7);

  // Filter products for the active collection tab strictly based on database categories
  const activeCollectionProducts = effectiveHomeProducts.filter(product => {
    if (!activeCollectionTab || activeCollectionTab.toUpperCase() === "ALL") return true;

    const activeTabLower = activeCollectionTab.toLowerCase().trim();
    const catNameLower = (product.category_relation?.name || "").toLowerCase().trim();
    const prodNameLower = (product.item_name || "").toLowerCase().trim();

    // 1. Direct match by Category Name
    if (catNameLower === activeTabLower || (catNameLower && (catNameLower.includes(activeTabLower) || activeTabLower.includes(catNameLower)))) {
      return true;
    }

    // 2. Direct match by Category ID
    const matchedCategory = categories.find(c => c.name && c.name.toLowerCase().trim() === activeTabLower);
    if (matchedCategory && (Number(product.category_id) === Number(matchedCategory.id) || Number(product.category_relation?.id) === Number(matchedCategory.id))) {
      return true;
    }

    // 3. Keyword match in product item_name
    return prodNameLower.includes(activeTabLower);
  });

  const displayProducts = activeCollectionProducts;

  // Generate 5 items for the Instagram Gallery
  const instagramGalleryItems = (() => {
    const placeholders = [
      "https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?q=80&w=400",
      "https://images.unsplash.com/photo-1529139574466-a303027c1d8b?q=80&w=400",
      "https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=400",
      "https://images.unsplash.com/photo-1509631179647-0177331693ae?q=80&w=400",
      "https://images.unsplash.com/photo-1496345875659-11f7dd282d1d?q=80&w=400"
    ];

    const items = [];
    const productsWithImages = homeProducts ? homeProducts.filter(p => p.web_main_image || p.image) : [];

    for (let i = 0; i < 5; i++) {
      if (productsWithImages[i]) {
        const product = productsWithImages[i];
        const image = product.web_main_image
          ? getAssetUrl(`uploads/products/${product.web_main_image}`)
          : product.image
          ? getAssetUrl(`uploads/products/${product.image}`)
          : getProductFallbackImage(product.id);
        items.push({
          id: product.id,
          image,
          link: `/product/${product.id}`,
          label: product.item_name,
          isPlaceholder: false
        });
      } else {
        items.push({
          id: `placeholder-${i}`,
          image: placeholders[i],
          link: settings?.web_instagram_link || "https://instagram.com",
          label: `@${settings?.web_site_name || "TrendHub"}`,
          isPlaceholder: true
        });
      }
    }
    return items;
  })();

  return (
    <div className="w-full overflow-hidden">
      {/* 1. HERO SECTION (EXACT REFERENCE UI DESIGN MATCH) */}
      <section className="relative w-full min-h-screen bg-neutral-950 text-white flex flex-col justify-between overflow-hidden pt-28 sm:pt-36 pb-0 select-none">
        {/* Full-width Background Media with 3D Parallax Scroll */}
        <motion.div 
          style={{ y: heroParallaxY, scale: heroMediaScale }}
          className="absolute inset-0 w-full h-full overflow-hidden z-0"
        >
          {videoError || (settings?.web_home_hero_media_type === "image" && settings?.web_home_hero_image) ? (
            <img
              src={settings?.web_home_hero_image ? getAssetUrl(settings.web_home_hero_image) : "https://images.unsplash.com/photo-1509631179647-0177331693ae?q=80&w=1600"}
              alt="Hero Background"
              className="w-full h-full object-cover filter brightness-[0.55] contrast-[1.1]"
            />
          ) : (
            <video
              ref={videoRef}
              autoPlay
              muted
              loop
              playsInline
              preload="metadata"
              onError={() => setVideoError(true)}
              className="w-full h-full object-cover filter brightness-[0.55] contrast-[1.1]"
            >
              <source
                src={settings?.web_home_hero_video ? getAssetUrl(settings.web_home_hero_video) : "/hero-video.mp4"}
                type="video/mp4"
              />
            </video>
          )}
          {/* Subtle Dark Vignette & Gradient Overlays */}
          <div className="absolute inset-0 bg-gradient-to-r from-black/80 via-black/40 to-transparent z-10" />
          <div className="absolute inset-0 bg-gradient-to-t from-neutral-950 via-transparent to-black/60 z-10" />
        </motion.div>

        {/* Main Hero Content (Fully Dynamic via Admin Settings) */}
        <div className="relative z-20 max-w-[1400px] mx-auto w-full px-6 sm:px-12 my-auto py-10 flex flex-col items-start justify-center space-y-5">
          <motion.div
            initial={{ opacity: 0, y: 25 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            className="space-y-2 max-w-[680px]"
          >
            <h1 className="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-sans font-bold tracking-tight text-white leading-tight uppercase">
              {settings?.web_hero_title || (settings?.web_site_name ? `${settings.web_site_name} Menswear` : "TrendHub Menswear")}
            </h1>
            <p className="text-sm sm:text-base md:text-lg font-sans text-gray-200 tracking-wide font-light">
              {settings?.web_hero_subtitle || "Elevated Men's Apparel — Shirts, Trousers, Polos & Tailored Shorts"}
            </p>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.2 }}
            className="text-[10px] sm:text-xs font-sans uppercase tracking-[0.2em] text-gray-300 font-semibold"
          >
            {settings?.web_hero_tagline || "FALL / WINTER 2026 COLLECTION • PREMIUM COTTON & LINEN FABRICS"}
          </motion.div>

          {/* Action Buttons (Fully Dynamic) */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.4 }}
            className="flex flex-wrap gap-3 pt-1"
          >
            <Link
              href={settings?.web_hero_btn1_link || "/shop"}
              className="bg-[#4F46E5] hover:bg-[#4338CA] text-white px-6 sm:px-7 py-2.5 sm:py-3 rounded-full text-[10px] sm:text-xs font-sans uppercase tracking-[0.2em] font-bold shadow-[0_0_20px_rgba(79,70,229,0.4)] transition-all duration-300 hover:scale-105"
            >
              {settings?.web_hero_btn1_text || "SHOP COLLECTION"}
            </Link>
            <Link
              href={settings?.web_hero_btn2_link || "/shop?promo_tag=Featured"}
              className="border border-white/40 hover:border-white text-white hover:bg-white/10 px-6 sm:px-7 py-2.5 sm:py-3 rounded-full text-[10px] sm:text-xs font-sans uppercase tracking-[0.2em] font-bold transition-all duration-300"
            >
              {settings?.web_hero_btn2_text || "EXPLORE MENSWEAR"}
            </Link>
          </motion.div>
        </div>

        {/* Hero Bottom Dock / 4 Featured Cards (Fully Dynamic via Admin Settings) */}
        <div className="relative z-20 w-full border-t border-white/20 bg-black/50 backdrop-blur-md">
          <div className="max-w-[1400px] mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-white/15">
            
            {/* Card 1 */}
            <Link
              href={settings?.web_card1_link || (homeProducts?.[0] ? `/product/${homeProducts[0].id}` : "/shop")}
              className="p-6 flex flex-col justify-between space-y-3 relative group hover:bg-white/5 transition-colors block"
            >
              <div className="w-14 h-0.5 bg-white mb-1" />
              <div>
                <span className="bg-[#4F46E5] text-white text-[9px] font-sans font-bold px-2.5 py-0.5 rounded uppercase tracking-wider inline-block mb-2">
                  {settings?.web_card1_badge || "FORMAL SHIRTS"}
                </span>
                <h4 className="text-sm font-sans font-bold text-white tracking-wide group-hover:text-indigo-400 transition-colors">
                  {settings?.web_card1_title || homeProducts?.[0]?.item_name || "Formal & Oxford Shirts"}
                </h4>
                <p className="text-xs text-gray-400 font-sans line-clamp-1 mt-0.5">
                  {settings?.web_card1_subtext || homeProducts?.[0]?.description || "100% Egyptian Cotton • Slim & Tailored Fits"}
                </p>
              </div>
              <div className="text-[10px] text-gray-400 font-sans tracking-wider uppercase pt-2 border-t border-white/10 flex justify-between items-center">
                <span>{settings?.web_card1_detail || "PREMIUM COTTON"}</span>
                <span className="text-indigo-300 font-bold">
                  {homeProducts?.[0]?.web_sale_price || homeProducts?.[0]?.sale_price_per_piece ? `RS. ${Number(homeProducts[0].web_sale_price || homeProducts[0].sale_price_per_piece).toLocaleString()}` : "SHOP NOW"}
                </span>
              </div>
            </Link>

            {/* Card 2 */}
            <Link
              href={settings?.web_card2_link || (homeProducts?.[1] ? `/product/${homeProducts[1].id}` : "/shop")}
              className="p-6 flex flex-col justify-between space-y-3 relative group hover:bg-white/5 transition-colors block"
            >
              <div>
                <span className="bg-red-600 text-white text-[9px] font-sans font-bold px-2.5 py-0.5 rounded uppercase tracking-wider inline-flex items-center gap-1.5 mb-2">
                  <span className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
                  {settings?.web_card2_badge || "FLASH SALE"}
                </span>
                <h4 className="text-sm font-sans font-bold text-white tracking-wide group-hover:text-indigo-400 transition-colors">
                  {settings?.web_card2_title || homeProducts?.[1]?.item_name || "Chino Trousers & Pants"}
                </h4>
                <p className="text-xs text-gray-400 font-sans line-clamp-1 mt-0.5">
                  {settings?.web_card2_subtext || homeProducts?.[1]?.description || "Flexible Stretch Cotton • Modern Slim Cut"}
                </p>
              </div>
              <div className="text-[10px] text-red-400 font-sans tracking-wider font-bold uppercase pt-2 border-t border-white/10 flex justify-between items-center">
                <span>{settings?.web_card2_detail || "UP TO 30% OFF"}</span>
                <span className="text-white font-bold">
                  {homeProducts?.[1]?.web_sale_price || homeProducts?.[1]?.sale_price_per_piece ? `RS. ${Number(homeProducts[1].web_sale_price || homeProducts[1].sale_price_per_piece).toLocaleString()}` : "EXPLORE"}
                </span>
              </div>
            </Link>

            {/* Card 3 */}
            <Link
              href={settings?.web_card3_link || (homeProducts?.[2] ? `/product/${homeProducts[2].id}` : "/shop")}
              className="p-6 flex flex-col justify-between space-y-3 relative group hover:bg-white/5 transition-colors block"
            >
              <div>
                <span className="bg-[#9333EA] text-white text-[9px] font-sans font-bold px-2.5 py-0.5 rounded uppercase tracking-wider inline-block mb-2">
                  {settings?.web_card3_badge || "POLO SHIRTS"}
                </span>
                <h4 className="text-sm font-sans font-bold text-white tracking-wide group-hover:text-indigo-400 transition-colors">
                  {settings?.web_card3_title || homeProducts?.[2]?.item_name || "Essential Polos & Tees"}
                </h4>
                <p className="text-xs text-gray-400 font-sans line-clamp-1 mt-0.5">
                  {settings?.web_card3_subtext || homeProducts?.[2]?.description || "Breathable Pima Fabric • Timeless Style"}
                </p>
              </div>
              <div className="text-[10px] text-gray-400 font-sans tracking-wider uppercase pt-2 border-t border-white/10 flex justify-between items-center">
                <span>{settings?.web_card3_detail || "BEST SELLER"}</span>
                <span className="text-indigo-300 font-bold">
                  {homeProducts?.[2]?.web_sale_price || homeProducts?.[2]?.sale_price_per_piece ? `RS. ${Number(homeProducts[2].web_sale_price || homeProducts[2].sale_price_per_piece).toLocaleString()}` : "EXPLORE"}
                </span>
              </div>
            </Link>

            {/* Card 4 */}
            <Link
              href={settings?.web_card4_link || (homeProducts?.[3] ? `/product/${homeProducts[3].id}` : "/shop")}
              className="p-6 flex flex-col justify-between space-y-3 relative group hover:bg-white/5 transition-colors block"
            >
              <div>
                <span className="bg-emerald-600 text-white text-[9px] font-sans font-bold px-2.5 py-0.5 rounded uppercase tracking-wider inline-block mb-2">
                  {settings?.web_card4_badge || "CASUAL SHORTS"}
                </span>
                <h4 className="text-sm font-sans font-bold text-white tracking-wide group-hover:text-indigo-400 transition-colors">
                  {settings?.web_card4_title || homeProducts?.[3]?.item_name || "Tailored Casual Shorts"}
                </h4>
                <p className="text-xs text-gray-400 font-sans line-clamp-1 mt-0.5">
                  {settings?.web_card4_subtext || homeProducts?.[3]?.description || "Relaxed Tailoring • Everyday Comfort"}
                </p>
              </div>
              <div className="text-[10px] text-gray-400 font-sans tracking-wider uppercase pt-2 border-t border-white/10 flex justify-between items-center">
                <span>{settings?.web_card4_detail || "NEW RELEASE"}</span>
                <span className="text-indigo-300 font-bold">
                  {homeProducts?.[3]?.web_sale_price || homeProducts?.[3]?.sale_price_per_piece ? `RS. ${Number(homeProducts[3].web_sale_price || homeProducts[3].sale_price_per_piece).toLocaleString()}` : "BUY NOW"}
                </span>
              </div>
            </Link>

          </div>
        </div>
      </section>

      {/* Scrollable Content Wrapper */}
      <div className="relative z-10 bg-white w-full shadow-[0_-15px_30px_rgba(0,0,0,0.08)]">
        
        {/* Continuous Horizontal Fashion Strip (With Fallback Model Imagery) */}
        <div className="relative z-10 w-full bg-neutral-900 border-t border-b border-neutral-800 py-3 select-none overflow-hidden">
          <div className="w-full relative flex items-center">
            {/* Slide Track */}
            <div className="animate-infinite-scroll flex gap-3 px-2">
              {homeProducts && homeProducts.length > 0 ? (
                [...homeProducts, ...homeProducts, ...homeProducts].map((product, index) => {
                  const pMainImage = product.web_main_image
                    ? getAssetUrl(`uploads/products/${product.web_main_image}`)
                    : product.image
                    ? getAssetUrl(`uploads/products/${product.image}`)
                    : getProductFallbackImage(product.id);
                  
                  return (
                    <Link
                      key={`${product.id}-${index}`}
                      href={`/product/${product.id}`}
                      className="w-[160px] sm:w-[200px] md:w-[240px] aspect-[4/5] shrink-0 relative bg-neutral-800 rounded-md overflow-hidden group border border-white/10 hover:border-indigo-500/50 transition-all duration-300"
                    >
                      <img
                        src={pMainImage}
                        alt={product.item_name}
                        className="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out brightness-90 group-hover:brightness-100"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-90 p-3 flex flex-col justify-end">
                        <span className="text-[10px] font-sans font-bold text-white uppercase tracking-wider line-clamp-1 group-hover:text-indigo-300 transition-colors">
                          {product.item_name}
                        </span>
                        <span className="text-[9px] font-sans font-semibold text-indigo-400">
                          Rs. {Number(product.web_sale_price || product.sale_price_per_piece || 2990).toLocaleString()}
                        </span>
                      </div>
                    </Link>
                  );
                })
              ) : (
                /* Fallback High-Res Menswear Model Cards (No Blank Boxes) */
                [...Array(12)].map((_, i) => (
                  <Link
                    key={i}
                    href="/shop"
                    className="w-[160px] sm:w-[200px] md:w-[240px] aspect-[4/5] shrink-0 relative bg-neutral-800 rounded-md overflow-hidden group border border-white/10 hover:border-indigo-500/50 transition-all duration-300"
                  >
                    <img
                      src={getProductFallbackImage(i)}
                      alt="Menswear Collection"
                      className="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent p-3 flex flex-col justify-end">
                      <span className="text-[10px] font-sans font-bold text-white uppercase tracking-wider">
                        TrendHub Apparel #{i + 1}
                      </span>
                      <span className="text-[9px] font-sans font-semibold text-indigo-400">
                        EXPLORE COLLECTION
                      </span>
                    </div>
                  </Link>
                ))
              )}
            </div>
          </div>
        </div>

        {/* 1.5 FEATURED COLLECTIONS SECTION (DYNAMIC SOFTWARE CATEGORIES & NEXT/PREV CAROUSEL SLIDER) */}
        <section className="py-20 bg-white overflow-hidden border-b border-gray-100">
          <div className="max-w-[1400px] mx-auto px-4 sm:px-6 space-y-10">
            
            <motion.div 
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
              className="space-y-3 text-center"
            >
              <motion.span 
                initial={{ opacity: 0, x: -40 }}
                whileInView={{ opacity: 1, x: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5 }}
                className="text-[10px] sm:text-xs font-sans font-bold uppercase tracking-[0.3em] text-indigo-600 block"
              >
                Exclusively Curated For You
              </motion.span>
              
              <motion.h2 
                initial={{ opacity: 0, x: 40 }}
                whileInView={{ opacity: 1, x: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.6 }}
                className="font-serif text-3xl sm:text-4xl md:text-5xl tracking-wide font-normal text-neutral-900 uppercase"
              >
                Featured Menswear Collections
              </motion.h2>
              
              <motion.p 
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: 0.2 }}
                className="text-xs sm:text-sm text-gray-500 max-w-[600px] mx-auto font-sans"
              >
                Discover modern luxury wardrobe staples designed with clean minimal aesthetics and timeless craftsmanship.
              </motion.p>
              {/* CENTERED Category Pills with Pagination Navigation */}
              <div className="flex items-center justify-center gap-2 max-w-[1000px] mx-auto px-2 pt-4">
                {categories.length > 5 && (
                  <button
                    onClick={() => scrollCategoryPills("left")}
                    className="w-8 h-8 rounded-full border border-gray-200 bg-white text-gray-700 hover:bg-black hover:text-white flex items-center justify-center shrink-0 transition-colors cursor-pointer shadow-xs"
                    aria-label="Previous Categories"
                  >
                    <ChevronLeft size={14} />
                  </button>
                )}

                <div 
                  ref={categoryPillsRef}
                  className="flex items-center gap-2 sm:gap-3 overflow-x-auto scrollbar-none scroll-smooth py-1 px-1 max-w-full justify-start sm:justify-center flex-nowrap sm:flex-wrap"
                  style={{ scrollbarWidth: "none", msOverflowStyle: "none" }}
                >
                  {categories.map((cat) => {
                    const isActive = activeCollectionTab === cat.name;
                    const catImage = cat.web_image_url || (cat.web_image ? getAssetUrl(cat.web_image) : null);
                    return (
                      <button
                        key={cat.id}
                        onClick={() => setActiveCollectionTab(cat.name)}
                        className={`px-5 sm:px-6 py-2 rounded-full text-xs font-sans font-bold tracking-widest uppercase transition-all duration-300 shrink-0 cursor-pointer flex items-center gap-2 ${
                          isActive
                            ? "bg-neutral-950 text-white shadow-md scale-105"
                            : "bg-gray-100 text-gray-600 hover:bg-gray-200"
                        }`}
                      >
                        {catImage && (
                          <img
                            src={getAssetUrl(catImage)}
                            alt={cat.name}
                            className="w-5 h-5 rounded-full object-cover shrink-0 border border-white/40"
                          />
                        )}
                        <span>{cat.name}</span>
                      </button>
                    );
                  })}
                </div>

                {categories.length > 5 && (
                  <button
                    onClick={() => scrollCategoryPills("right")}
                    className="w-8 h-8 rounded-full border border-gray-200 bg-white text-gray-700 hover:bg-black hover:text-white flex items-center justify-center shrink-0 transition-colors cursor-pointer shadow-xs"
                    aria-label="Next Categories"
                  >
                    <ChevronRight size={14} />
                  </button>
                )}
              </div>
            </motion.div>

            {/* Slider Navigation Bar with < and > Controls */}
            <div className="flex items-center justify-between border-b border-gray-100 pb-3 px-2 pt-2">
              <span className="text-xs font-sans font-bold uppercase tracking-widest text-neutral-400">
                {activeCollectionTab === "ALL" ? "All Items" : activeCollectionTab} ({displayProducts.length})
              </span>
              <div className="flex items-center gap-2">
                <button
                  onClick={() => scrollCollectionProducts("left")}
                  className="w-9 h-9 rounded-full border border-gray-200 bg-white text-neutral-800 hover:bg-neutral-900 hover:text-white flex items-center justify-center transition-all shadow-sm cursor-pointer hover:scale-105 active:scale-95"
                  aria-label="Previous Products"
                >
                  <ChevronLeft size={16} />
                </button>
                <button
                  onClick={() => scrollCollectionProducts("right")}
                  className="w-9 h-9 rounded-full border border-gray-200 bg-white text-neutral-800 hover:bg-neutral-900 hover:text-white flex items-center justify-center transition-all shadow-sm cursor-pointer hover:scale-105 active:scale-95"
                  aria-label="Next Products"
                >
                  <ChevronRight size={16} />
                </button>
              </div>
            </div>

            {/* Products Horizontal Carousel Slider */}
            {displayProducts.length > 0 ? (
              <div 
                ref={collectionSliderRef}
                className="flex gap-6 overflow-x-auto scrollbar-none scroll-smooth pb-4 select-none"
                style={{ scrollbarWidth: "none", msOverflowStyle: "none" }}
              >
                {displayProducts.map((product) => (
                  <div key={product.id} className="w-[240px] sm:w-[270px] md:w-[290px] shrink-0">
                    <ProductCard product={product} />
                  </div>
                ))}
              </div>
            ) : (
              <div className="py-12 text-center text-gray-400 font-sans text-xs tracking-widest uppercase">
                No active products in <span className="font-bold text-neutral-800">{activeCollectionTab}</span> category yet.
              </div>
            )}

            {/* Bottom Explore Link */}
            <div className="text-center pt-2">
              <Link
                href="/shop"
                className="bg-[#333333] hover:bg-neutral-900 text-white px-8 py-3.5 text-xs uppercase tracking-[0.2em] font-sans font-bold transition-all duration-300 inline-block rounded-xs hover:scale-105"
              >
                View all products
              </Link>
            </div>
        </div>
      </section>

      {/* 2. DYNAMIC NEW ARRIVALS SLIDER (REFERENCE MATCH) */}
      {newArrivals && newArrivals.length > 0 && (
        <section className="w-full bg-white py-20 overflow-hidden border-t border-gray-100">
          <div className="max-w-[1400px] mx-auto px-6 flex flex-col lg:flex-row items-center">
            
            {/* Left: Large Featured Image */}
            <div className="w-full lg:w-[38%] xl:w-[32%] relative aspect-[4/5] sm:aspect-[3/4] lg:aspect-[4/5] bg-gray-200 overflow-hidden group rounded-[2px] z-0">
              <img 
                src={settings?.web_home_banner_image ? getAssetUrl(settings.web_home_banner_image) : "https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?q=80&w=800"} 
                alt="New Arrivals" 
                className="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
                loading="lazy"
              />
              <div className="absolute inset-0 bg-black/5"></div>
            </div>

            {/* Right: Category Slider (Overlapping) */}
            <div className="w-full lg:w-[68%] xl:w-[73%] flex flex-col justify-center pt-8 lg:pt-0 lg:-ml-24 xl:-ml-28 z-10 overflow-hidden">
              
              {/* Header & Controls */}
              <div className="flex flex-col sm:flex-row justify-between items-center mb-8 space-y-4 sm:space-y-0 p-2 lg:p-0">
                <div className="space-y-1 text-center w-full">
                  <h2 className="font-serif text-3xl sm:text-4xl lg:text-5xl tracking-wide font-light text-black">
                    New Arrivals
                  </h2>
                </div>
                
                {/* Slider Arrows (Positioned at right edge) */}
                <div className="flex gap-2 sm:absolute sm:right-6 lg:right-0">
                  <button 
                    onClick={() => scrollCategories("left")}
                    className="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-black hover:text-white transition-colors disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-black cursor-pointer bg-white"
                  >
                    <ChevronLeft size={20} strokeWidth={1.5} />
                  </button>
                  <button 
                    onClick={() => scrollCategories("right")}
                    className="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-black hover:text-white transition-colors cursor-pointer bg-white"
                  >
                    <ChevronRight size={20} strokeWidth={1.5} />
                  </button>
                </div>
              </div>

              {/* Slider Track */}
              <div 
                ref={sliderRef}
                className="flex gap-2 sm:gap-2.5 overflow-x-auto snap-x snap-mandatory pb-4"
                style={{ scrollbarWidth: 'none', msOverflowStyle: 'none' }}
              >
                {/* Hide webkit scrollbar via inline styles equivalent */}
                <style dangerouslySetInnerHTML={{__html: `::-webkit-scrollbar { display: none; }`}} />
                
                {newArrivals.map((product) => {
                  const pMainImage = product.web_main_image
                    ? getAssetUrl(`uploads/products/${product.web_main_image}`)
                    : product.image
                    ? getAssetUrl(`uploads/products/${product.image}`)
                    : getProductFallbackImage(product.id);

                  return (
                    <Link
                      key={product.id}
                      href={`/product/${product.id}`}
                      className="group relative flex-none w-full sm:w-[calc((100%-10px)/2)] lg:w-[calc((100%-20px)/3)] aspect-[3/4] bg-gray-100 overflow-hidden snap-start block rounded-[2px]"
                    >
                      <img
                        src={pMainImage}
                        alt={product.item_name}
                        className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        loading="lazy"
                      />
                        {/* Name Label Overlay displaying Category matching the Reference Image */}
                        <div className="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/50 text-white px-4 py-1.5 text-[9px] uppercase tracking-[0.2em] font-sans font-bold whitespace-nowrap rounded-[2px] transition-all duration-300 group-hover:bg-black/75">
                          {product.category_relation?.name || "Collection"}
                        </div>
                    </Link>
                  );
                })}
                {/* Spacer to act as padding-right at the end of the scroll */}
                <div className="flex-none w-4 sm:w-6 lg:w-12 h-1" />
              </div>

            </div>
          </div>
        </section>
      )}

      {/* 3. TRENDING PRODUCTS */}
      {trendingProducts.length > 0 && (
        <section className="bg-[#fafafa] py-20 overflow-hidden">
          <div className="max-w-[1400px] mx-auto px-6 space-y-10">
            <motion.div 
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
              className="text-center space-y-4"
            >
              <h2 className="font-serif text-3xl sm:text-4xl tracking-wide font-normal text-neutral-800 uppercase">
                Trending Now
              </h2>
              <p className="text-[10px] text-gray-400 uppercase tracking-widest font-sans">Most wanted luxury essentials</p>
            </motion.div>

            <motion.div 
              initial={{ opacity: 0, y: 40 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="grid grid-cols-2 md:grid-cols-5 gap-5"
            >
              {trendingProducts.slice(0, 5).map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </motion.div>

            <motion.div 
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: 0.3 }}
              className="pt-6 text-center"
            >
              <Link
                href="/shop?promo_tag=Trending"
                className="bg-[#333333] hover:bg-neutral-900 text-white px-8 py-3 text-[10px] sm:text-xs uppercase tracking-[0.2em] font-sans font-bold transition-all duration-300 inline-block rounded-xs hover:scale-105"
              >
                View all trending
              </Link>
            </motion.div>
          </div>
        </section>
      )}




      {/* 8. STORE LOCATOR BANNER (HIGH CONTRAST LUXURY DESIGN) */}
      <section className="w-full bg-neutral-900 border-t border-neutral-800 select-none overflow-hidden">
        <Link href="/store-locator" className="block relative w-full h-[320px] sm:h-[480px] overflow-hidden group">
          <motion.div style={{ y: storeParallaxY }} className="w-full h-[120%] -mt-[10%]">
            <img
              src={settings?.web_store_locator_banner_image 
                ? getAssetUrl(settings.web_store_locator_banner_image) 
                : "https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=1600"}
              alt="Store Locator"
              className="w-full h-full object-cover object-center filter brightness-75 group-hover:scale-105 transition-transform duration-1000"
            />
          </motion.div>
          <div className="absolute inset-0 bg-gradient-to-t from-black/90 via-black/60 to-black/40 flex flex-col items-center justify-center p-6 text-center space-y-4">
            <span className="text-[10px] sm:text-xs font-sans font-bold uppercase tracking-[0.3em] text-indigo-400">
              Visit Our Exclusive Outlets
            </span>
            <h3 className="text-white text-3xl sm:text-5xl md:text-6xl uppercase tracking-[0.2em] font-serif font-light drop-shadow-md">
              STORE LOCATOR
            </h3>
            <p className="text-gray-300 text-xs sm:text-sm uppercase tracking-widest font-sans font-light max-w-[500px]">
              Discover our flagship menswear stores near you. Your favorites are now just a visit away!
            </p>
            <span className="mt-4 bg-white hover:bg-indigo-600 text-black hover:text-white text-[10px] sm:text-xs uppercase tracking-[0.25em] font-sans font-bold px-8 py-3.5 transition-all duration-300 shadow-xl rounded-xs">
              FIND A STORE NEAR YOU
            </span>
          </div>
        </Link>
      </section>
      </div>
    </div>
  );
}
