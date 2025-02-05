"use client";

export default function LoadingSpinner() {
    return (
        <div className="flex items-center justify-center space-x-2 mt-4">
            <div className="w-4 h-4 border-2 border-blue-500 rounded-full border-t-transparent animate-spin"></div>
            <p className="text-blue-600 font-semibold">Carregando...</p>
        </div>
    );
}
