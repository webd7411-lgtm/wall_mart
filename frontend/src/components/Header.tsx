"use client";

import { useState, useEffect, useRef } from "react";
import { Search, Heart, User, ShoppingBag, Menu, X } from "lucide-react";
import Link from "next/link";
import { motion, AnimatePresence } from "framer-motion";
import { usePathname } from "next/navigation";
import { useCartStore } from "@/store/cartStore";
import { useWishlistStore } from "@/store/wishlistStore";
import { useAuthStore } from "@/store/authStore";
import CartSidebar from "./CartSidebar";
import AnnouncementBar from "./AnnouncementBar";
import { useSettings } from "@/hooks/useSettings";
import { Product, Category } from "@/types";
import api from "@/lib/api";
import { getProductFallbackImage, getAssetUrl } from "@/lib/imageHelper";
import { useQuery } from "@tanstack/react-query";

export default function Header() {
  const pathname = usePathname();
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [isCartOpen, setIsCartOpen] = useState(false);
  const [isSearchOpen, setIsSearchOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");
  const [searchResults, setSearchResults] = useState<Product[]>([]);
  const [isSearching, setIsSearching] = useState(false);
  const [suggestions, setSuggestions] = useState<string[]>([]);
  const [isMounted, setIsMounted] = useState(false);
  const [isVisible, setIsVisible] = useState(true);
  const lastScrollY = useRef(0);

  const cartItemsCount = useCartStore((state) => state.getTotalItems());
  const wishlistItemsCount = useWishlistStore((state) => state.items.length);
  const user = useAuthStore((state) => state.user);
  const checkAuth = useAuthStore((state) => state.checkAuth);

  const { data: settings } = useSettings();

  const { data: categories } = useQuery({
    queryKey: ["header-categories"],
    queryFn: async () => {
      const res = await api.get("/categories");
      return res.data?.data as Category[] || [];
    },
  });

  useEffect(() => {
    setIsMounted(true);
    checkAuth();
  }, []);

  // Debounced live product search
  useEffect(() => {
    if (searchQuery.trim() === "") {
      setSearchResults([]);
      setSuggestions([]);
      return;
    }

    setIsSearching(true);
    const delayDebounce = setTimeout(async () => {
      try {
        const res = await api.get(`/products?search=${encodeURIComponent(searchQuery)}`);
        const fetchedProducts = res.data?.data?.data as Product[] || [];
        setSearchResults(fetchedProducts);

        // Predefined keywords to filter suggestions
        const allPossibleSuggestions = [
          "Formal Shirts",
          "Polo Shirts",
          "Denim Jackets",
          "Trousers",
          "Knit Wool Sweater",
          "Oxford Shoes",
          "Utility Jacket",
          "Evening Gown"
        ];
        const filtered = allPossibleSuggestions.filter(item => 
          item.toLowerCase().includes(searchQuery.toLowerCase())
        );
        setSuggestions(filtered);
      } catch (error) {
        console.error("Live search error:", error);
      } finally {
        setIsSearching(false);
      }
    }, 300);

    return () => clearTimeout(delayDebounce);
  }, [searchQuery]);

  // Scroll handler for transparent/white header logic + auto-hide and open-cart event
  useEffect(() => {
    const handleScroll = () => {
      const currentScrollY = window.scrollY;

      // Handle transparent vs solid background logic
      if (currentScrollY > 50) {
        setIsScrolled(true);
      } else {
        setIsScrolled(false);
      }

      // Handle show/hide header on scroll logic
      if (currentScrollY > lastScrollY.current && currentScrollY > 100) {
        // Scrolling down and past threshold -> Hide header
        setIsVisible(false);
      } else {
        // Scrolling up or near top -> Show header
        setIsVisible(true);
      }

      lastScrollY.current = currentScrollY;
    };

    const handleOpenCart = () => {
      setIsCartOpen(true);
    };

    window.addEventListener("scroll", handleScroll);
    window.addEventListener("open-cart", handleOpenCart);

    return () => {
      window.removeEventListener("scroll", handleScroll);
      window.removeEventListener("open-cart", handleOpenCart);
    };
  }, []);

  const isHome = pathname === "/";
  const logoUrl = settings?.company_logo || settings?.web_site_logo || settings?.site_logo || settings?.logo_url || settings?.logo;

  return (
    <>
      <header className={`w-full z-40 ${isHome && !isScrolled ? "absolute top-0 left-0 bg-transparent" : "relative"}`}>
        {/* <AnnouncementBar /> */}

        {/* Main Navbar */}
        <nav
          className={`w-full transition-all duration-300 transform ${
            isVisible ? "translate-y-0" : "-translate-y-full"
          } ${
            isScrolled || !isHome
              ? "fixed top-0 left-0 w-full bg-neutral-950/95 backdrop-blur-md shadow-2xl text-white border-b border-white/10 z-50"
              : "absolute top-0 left-0 w-full bg-transparent text-white border-b border-white/15 z-50"
          }`}
        >
          <div className="max-w-[1400px] mx-auto px-4 sm:px-8 py-2.5 flex items-center justify-between">
            {/* Left Nav Links (Garments Store Style) */}
            <div className="hidden lg:flex items-center gap-4 text-[10px] sm:text-xs font-sans font-bold tracking-[0.2em] uppercase border-y border-white/20 py-1.5 px-5">
              <Link href="/shop" className="hover:text-indigo-400 transition-colors duration-200">
                Shop Men
              </Link>
              <span className="text-white/30 font-light">|</span>
              <Link href="/shop?promo_tag=New Arrival" className="hover:text-indigo-400 transition-colors duration-200">
                New Arrivals
              </Link>
              <span className="text-white/30 font-light">|</span>
              <Link href="/shop?promo_tag=Trending" className="hover:text-indigo-400 transition-colors duration-200">
                Best Sellers
              </Link>
            </div>

            {/* Mobile Hamburger Menu Button */}
            <div className="flex lg:hidden items-center">
              <button
                onClick={() => setIsMobileMenuOpen(true)}
                className="flex items-center gap-1.5 cursor-pointer focus:outline-none hover:opacity-80 p-2 text-white"
                aria-label="Open menu"
              >
                <Menu size={20} />
                <span className="text-[10px] uppercase tracking-[0.2em] font-sans font-semibold">Menu</span>
              </button>
            </div>

            {/* Center Framed Brand Box (Sleek Refined Design with Logo Support) */}
            <div className="flex flex-col items-center justify-center">
              <div className="relative border border-white/35 bg-black/70 backdrop-blur-md px-5 sm:px-7 py-1 text-center flex flex-col items-center rounded-[2px] hover:border-white/70 transition-all duration-300 shadow-xl group">
                {/* Subtle top corner accent lines */}
                <span className="absolute -top-0.5 left-2 w-2.5 h-[1px] bg-white/70" />
                <span className="absolute -top-0.5 right-2 w-2.5 h-[1px] bg-white/70" />
                
                <Link
                  href="/"
                  className="flex items-center justify-center font-serif text-xs sm:text-sm md:text-base tracking-[0.3em] font-semibold uppercase text-white hover:opacity-90 transition-opacity"
                >
                  {logoUrl ? (
                    <img
                      src={getAssetUrl(logoUrl)}
                      alt={settings?.web_site_name || "TRENDHUB STORE"}
                      className="h-6 sm:h-7 w-auto max-w-[170px] object-contain py-0.5 filter brightness-100 group-hover:scale-[1.02] transition-transform duration-300"
                    />
                  ) : (
                    <span className="font-serif tracking-[0.3em] font-bold text-white text-xs sm:text-sm md:text-base">
                      {settings?.web_site_name || "TRENDHUB STORE"}
                    </span>
                  )}
                </Link>
                <div className="w-full border-t border-white/20 mt-0.5 pt-0.5 flex items-center justify-center gap-2 text-[7.5px] sm:text-[8px] uppercase tracking-[0.2em] font-sans text-white/80">
                  <Link href={user ? "/dashboard" : "/login"} className="hover:text-indigo-300 transition-colors duration-200">
                    {user ? "MY ACCOUNT" : "LOG IN / REGISTER"}
                  </Link>
                  <span className="text-white/40">•</span>
                  <button onClick={() => setIsCartOpen(true)} className="hover:text-indigo-300 transition-colors duration-200 cursor-pointer">
                    SHOPPING BAG ({isMounted ? cartItemsCount : 0})
                  </button>
                </div>
              </div>
            </div>

            {/* Right Nav Links (Garments Store Style) */}
            <div className="hidden lg:flex items-center gap-4 text-[10px] sm:text-xs font-sans font-bold tracking-[0.2em] uppercase border-y border-white/20 py-1.5 px-5">
              <Link href="/store-locator" className="hover:text-indigo-400 transition-colors duration-200">
                Store Locator
              </Link>
              <span className="text-white/30 font-light">|</span>
              <Link href="/wishlist" className="hover:text-indigo-400 transition-colors duration-200 relative">
                Wishlist
                {isMounted && wishlistItemsCount > 0 && (
                  <span className="ml-1.5 bg-indigo-500 text-white text-[9px] px-1.5 py-0.5 rounded-full font-bold">
                    {wishlistItemsCount}
                  </span>
                )}
              </Link>
              <span className="text-white/30 font-light">|</span>
              <button
                onClick={() => setIsSearchOpen(true)}
                className="flex items-center gap-1.5 hover:text-indigo-400 transition-colors duration-200 cursor-pointer"
              >
                <Search size={14} />
                <span>Search</span>
              </button>
            </div>

            {/* Mobile Action Buttons */}
            <div className="flex lg:hidden items-center gap-2">
              <button
                onClick={() => setIsSearchOpen(true)}
                className="p-1.5 hover:opacity-80 cursor-pointer"
                aria-label="Search"
              >
                <Search size={18} />
              </button>
              <button
                onClick={() => setIsCartOpen(true)}
                className="p-1.5 hover:opacity-80 relative cursor-pointer"
                aria-label="Cart"
              >
                <ShoppingBag size={18} />
                {isMounted && cartItemsCount > 0 && (
                  <span className="absolute -top-1 -right-1 bg-indigo-600 text-white text-[8px] w-4 h-4 rounded-full flex items-center justify-center font-bold">
                    {cartItemsCount}
                  </span>
                )}
              </button>
            </div>
          </div>
        </nav>

        {/* Animated Drawer Menu */}
        <AnimatePresence>
          {isMobileMenuOpen && (
            <>
              {/* Overlay */}
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 0.4 }}
                exit={{ opacity: 0 }}
                onClick={() => setIsMobileMenuOpen(false)}
                className="fixed inset-0 bg-black z-50 cursor-pointer"
              />

              {/* Drawer Content */}
              <motion.div
                initial={{ x: "-100%" }}
                animate={{ x: 0 }}
                exit={{ x: "-100%" }}
                transition={{ type: "tween", duration: 0.35, ease: "easeInOut" }}
                className="fixed left-0 top-0 bottom-0 w-full max-w-[360px] sm:max-w-[400px] bg-white z-50 flex flex-col shadow-2xl text-black border-r border-gray-100"
              >
                {/* Header */}
                <div className="p-6 border-b border-gray-100 flex items-center justify-between">
                  <span className="font-sans text-sm font-bold uppercase tracking-[0.2em] text-black">Menu</span>
                  <button
                    onClick={() => setIsMobileMenuOpen(false)}
                    className="p-1.5 hover:bg-gray-50 rounded-full transition-colors text-gray-500 hover:text-black cursor-pointer"
                  >
                    <X size={20} />
                  </button>
                </div>

                {/* Links */}
                <div className="flex-1 overflow-y-auto px-8 py-10 space-y-8">
                  {/* Main Sections */}
                  {categories && categories.length > 0 && (
                    <div className="flex flex-col">
                      <span className="font-sans text-xs font-bold uppercase tracking-[0.2em] text-black mb-4 block">
                        Shop by Category
                      </span>
                      <div className="flex flex-col space-y-4">
                        {categories.map((cat) => (
                          <Link
                            key={cat.id}
                            href={`/shop?category_id=${cat.id}`}
                            onClick={() => setIsMobileMenuOpen(false)}
                            className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors block hover:translate-x-1.5 transition-transform duration-200"
                          >
                            {cat.name}
                          </Link>
                        ))}
                      </div>
                      <div className="border-t border-gray-100 my-4" />
                    </div>
                  )}

                  {/* Collections */}
                  <div className="flex flex-col space-y-4">
                    <Link
                      href="/shop"
                      onClick={() => setIsMobileMenuOpen(false)}
                      className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors block"
                    >
                      Shop All
                    </Link>
                    <Link
                      href="/shop?promo_tag=New Arrival"
                      onClick={() => setIsMobileMenuOpen(false)}
                      className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors block"
                    >
                      New Arrivals
                    </Link>
                    <Link
                      href="/shop?promo_tag=Trending"
                      onClick={() => setIsMobileMenuOpen(false)}
                      className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors block"
                    >
                      Trending
                    </Link>
                    <Link
                      href="/shop?promo_tag=Flash Sale"
                      onClick={() => setIsMobileMenuOpen(false)}
                      className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors block"
                    >
                      Sale
                    </Link>
                  </div>

                  <div className="border-t border-gray-100 my-4" />

                  {/* User Actions */}
                  <div className="flex flex-col space-y-4">
                    <Link
                      href="/wishlist"
                      onClick={() => setIsMobileMenuOpen(false)}
                      className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors flex items-center justify-between"
                    >
                      <span>Wishlist</span>
                      {isMounted && wishlistItemsCount > 0 && (
                        <span className="bg-black text-white px-2 py-0.5 rounded-full text-[10px] font-bold">
                          {wishlistItemsCount}
                        </span>
                      )}
                    </Link>
                    <Link
                      href={user ? "/dashboard" : "/login"}
                      onClick={() => setIsMobileMenuOpen(false)}
                      className="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-gray-600 hover:text-black transition-colors block"
                    >
                      {user ? "My Account" : "Login / Register"}
                    </Link>
                  </div>
                </div>
              </motion.div>
            </>
          )}
        </AnimatePresence>

        {/* Search Overlay */}
        {isSearchOpen && (
          <div className="fixed inset-0 bg-white z-50 flex flex-col p-6 overflow-y-auto">
            <div className="max-w-[1200px] mx-auto w-full mt-10 flex flex-col flex-1 pb-10">
              {/* Search Header */}
              <div className="flex justify-between items-center border-b border-black pb-4">
                <div className="flex items-center gap-3 flex-1">
                  <Search size={24} className="text-black" />
                  <input
                    type="text"
                    placeholder="SEARCH FOR LUXURY APPAREL..."
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    onKeyDown={(e) => {
                      if (e.key === "Enter" && searchQuery.trim() !== "") {
                        setIsSearchOpen(false);
                        window.location.href = `/shop?search=${encodeURIComponent(searchQuery)}`;
                      }
                    }}
                    className="w-full bg-transparent text-xl font-serif tracking-widest focus:outline-none placeholder-gray-300 text-black uppercase"
                    autoFocus
                  />
                </div>
                <button onClick={() => {
                  setIsSearchOpen(false);
                  setSearchQuery("");
                }} className="p-2 border border-gray-100 hover:border-black rounded-full transition-colors">
                  <X size={24} />
                </button>
              </div>

              {/* Dynamic Content Area */}
              {searchQuery.trim() === "" ? (
                /* Popular Searches (Empty state) */
                <div className="mt-10">
                  <h5 className="text-xs font-semibold tracking-widest text-gray-400 uppercase font-sans mb-4">Popular Searches</h5>
                  <div className="flex flex-wrap gap-3">
                    {["Formal Shirts", "Polo Shirts", "Denim Jackets", "Trousers"].map((term) => (
                      <Link
                        key={term}
                        href={`/shop?search=${encodeURIComponent(term)}`}
                        onClick={() => setIsSearchOpen(false)}
                        className="border border-gray-200 hover:border-black px-5 py-2.5 text-xs font-sans uppercase tracking-wider transition-colors text-black rounded-full"
                      >
                        {term}
                      </Link>
                    ))}
                  </div>
                </div>
              ) : (
                /* Active Search State */
                <div className="flex flex-col flex-1 mt-8 space-y-8">
                  {/* Suggestions Row */}
                  {suggestions.length > 0 && (
                    <div className="border-b border-gray-100 pb-6">
                      <h5 className="text-xs font-semibold tracking-widest text-gray-400 uppercase font-sans mb-4">Suggestions</h5>
                      <div className="flex flex-wrap gap-3">
                        {suggestions.map((sug) => {
                          // Highlight query inside suggestion
                          const parts = sug.split(new RegExp(`(${searchQuery})`, 'gi'));
                          return (
                            <Link
                              key={sug}
                              href={`/shop?search=${encodeURIComponent(sug)}`}
                              onClick={() => setIsSearchOpen(false)}
                              className="bg-gray-50 border border-gray-100 hover:border-black rounded-full px-4 py-2 text-xs font-sans uppercase tracking-wider flex items-center gap-2 transition-colors text-black"
                            >
                              <Search size={12} className="text-gray-400" />
                              <span>
                                {parts.map((part, i) => 
                                  part.toLowerCase() === searchQuery.toLowerCase() ? (
                                    <mark key={i} className="bg-yellow-100 text-black px-0.5 rounded font-bold">{part}</mark>
                                  ) : (
                                    <span key={i}>{part}</span>
                                  )
                                )}
                              </span>
                            </Link>
                          );
                        })}
                      </div>
                    </div>
                  )}

                  {/* Product Results */}
                  <div className="flex-1">
                    <h5 className="text-xs font-semibold tracking-widest text-gray-400 uppercase font-sans mb-6">
                      Product Results {isSearching ? "" : `(${searchResults.length})`}
                    </h5>

                    {isSearching ? (
                      <div className="flex flex-col items-center justify-center py-20 space-y-4">
                        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-black"></div>
                        <span className="text-xs text-gray-400 font-sans tracking-widest uppercase">Searching products...</span>
                      </div>
                    ) : searchResults.length > 0 ? (
                      <div className="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-6">
                        {searchResults.map((product) => {
                          const pMainImage = product.web_main_image
                            ? getAssetUrl(`uploads/products/${product.web_main_image}`)
                            : product.image
                            ? getAssetUrl(`uploads/products/${product.image}`)
                            : getProductFallbackImage(product.id);
                            
                          const price = product.web_sale_price || product.sale_price_per_piece;

                          return (
                            <Link
                              key={product.id}
                              href={`/product/${product.id}`}
                              onClick={() => {
                                setIsSearchOpen(false);
                                setSearchQuery("");
                              }}
                              className="group flex flex-col space-y-3 block"
                            >
                              <div className="aspect-[3/4] w-full bg-gray-50 overflow-hidden relative border border-gray-100 rounded-md">
                                <img
                                  src={pMainImage}
                                  alt={product.item_name}
                                  className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                />
                              </div>
                              <div className="space-y-1">
                                <h4 className="text-[11px] font-sans font-bold uppercase tracking-wider text-black line-clamp-2 group-hover:underline leading-relaxed">
                                  {product.item_name}
                                </h4>
                                <p className="text-[10px] text-gray-500 font-sans font-bold">
                                  Rs. {Number(price).toLocaleString()}
                                </p>
                              </div>
                            </Link>
                          );
                        })}
                      </div>
                    ) : (
                      <div className="flex flex-col items-center justify-center py-20 text-center space-y-2">
                        <span className="text-sm text-gray-400 font-serif italic">No luxury apparel found matching "{searchQuery}"</span>
                        <span className="text-xs text-gray-300 font-sans tracking-wider uppercase">Try checking spelling or try other keywords</span>
                      </div>
                    )}
                  </div>
                </div>
              )}
            </div>
          </div>
        )}
      </header>

      {/* Cart Sidebar Drawer */}
      <CartSidebar isOpen={isCartOpen} onClose={() => setIsCartOpen(false)} />
    </>
  );
}
