'use client';

import { useEffect } from 'react';
import Link from 'next/link';

export default function Error({
  error,
  reset,
}: {
  error: Error & { digest?: string };
  reset: () => void;
}) {
  useEffect(() => {
    console.error('TrendHub Client Error:', error);
  }, [error]);

  return (
    <div className="min-h-[70vh] flex flex-col items-center justify-center px-6 text-center font-sans select-none bg-white">
      <div className="space-y-4 max-w-md">
        <h2 className="font-serif text-3xl sm:text-4xl uppercase tracking-[0.2em] font-light text-black">
          Something went wrong
        </h2>
        <p className="text-xs sm:text-sm text-gray-500 font-light leading-relaxed">
          We experienced a temporary glitch while rendering this page. Please try refreshing or return to the main storefront.
        </p>
        <div className="pt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
          <button
            onClick={() => reset()}
            className="w-full sm:w-auto bg-black text-white px-8 py-3 text-xs uppercase tracking-[0.2em] font-bold hover:bg-neutral-800 transition-colors"
          >
            Try Again
          </button>
          <Link
            href="/"
            className="w-full sm:w-auto border border-black text-black px-8 py-3 text-xs uppercase tracking-[0.2em] font-bold hover:bg-black hover:text-white transition-all"
          >
            Return Home
          </Link>
        </div>
      </div>
    </div>
  );
}
