import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { useState, useRef, useEffect } from 'react';
import axios from 'axios';

// Import React Icons
import {
  FiBook, FiUsers, FiFolder, FiPackage, FiDollarSign,
  FiAlertTriangle, FiTrendingUp, FiTrendingDown, FiMessageCircle,
  FiSend, FiX, FiPlus, FiUserPlus, FiGrid, FiBarChart2,
  FiCalendar, FiActivity, FiShoppingBag, FiStar, FiClock
} from 'react-icons/fi';
import {
  MdOutlineLibraryBooks, MdOutlineCategory, MdOutlineInventory,
  MdOutlineAttachMoney, MdOutlineWarning, MdOutlineChat,
  MdOutlineDashboard, MdOutlineTrendingUp, MdOutlineTrendingDown
} from 'react-icons/md';
import {
  FaBook, FaUserPlus, FaChartLine, FaRocket, FaRobot
} from 'react-icons/fa';
import {
  HiOutlineChatAlt2, HiOutlineUsers, HiOutlineBookOpen
} from 'react-icons/hi';
import {
  BsGraphUp, BsGraphDown, BsChatDots, BsPlusCircle
} from 'react-icons/bs';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface Message {
    id: number;
    role: 'user' | 'assistant';
    content: string;
    books?: Book[];
}

interface Book {
    id: number;
    title: string;
    author: string;
    price: number;
    stock: number;
    category?: {
        id: number;
        name: string;
    };
    publisher?: string;
    year?: number;
}

interface DashboardStats {
    totalBooks: number;
    totalUsers: number;
    totalCategories: number;
    totalStock: number;
    totalValue: number;
    lowStock: number;
    monthlySales: { month: string; sales: number }[];
    categoryDistribution: { name: string; count: number }[];
}

// Modern Stat Card Component with Icons
function StatCard({ title, value, icon: Icon, trend, trendValue, color }: {
    title: string;
    value: number | string;
    icon: React.ElementType;
    trend?: 'up' | 'down';
    trendValue?: string;
    color: string;
}) {
    return (
        <div className={`group relative overflow-hidden rounded-2xl bg-gradient-to-br ${color} p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-1`}>
            <div className="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10 blur-2xl transition-all duration-300 group-hover:scale-150" />
            <div className="relative z-10">
                <div className="mb-3 flex items-center justify-between">
                    <Icon className="text-4xl text-white/90" />
                    {trend && (
                        <span className={`flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium backdrop-blur-sm ${trend === 'up' ? 'bg-green-500/30 text-green-100' : 'bg-red-500/30 text-red-100'}`}>
                            {trend === 'up' ? <FiTrendingUp className="h-3 w-3" /> : <FiTrendingDown className="h-3 w-3" />}
                            {trendValue}
                        </span>
                    )}
                </div>
                <p className="text-sm font-medium text-white/80">{title}</p>
                <p className="mt-1 text-3xl font-bold text-white">{typeof value === 'number' ? value.toLocaleString() : value}</p>
            </div>
        </div>
    );
}

// Modern Bar Chart Component
function ModernBarChart({ data }: { data: { label: string; value: number; color: string }[] }) {
    const maxValue = Math.max(...data.map(d => d.value));

    return (
        <div className="space-y-4">
            {data.map((item, idx) => (
                <div key={idx} className="group">
                    <div className="mb-1 flex justify-between text-sm">
                        <span className="font-medium text-gray-700 dark:text-gray-300">{item.label}</span>
                        <span className="text-gray-500 dark:text-gray-400">{item.value} buku</span>
                    </div>
                    <div className="relative h-10 w-full overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800">
                        <div
                            className={`absolute left-0 top-0 h-full rounded-xl ${item.color} transition-all duration-700 ease-out group-hover:opacity-90`}
                            style={{ width: `${(item.value / maxValue) * 100}%` }}
                        >
                            <div className="absolute right-2 top-1/2 -translate-y-1/2 text-xs font-semibold text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                {item.value}
                            </div>
                        </div>
                    </div>
                </div>
            ))}
        </div>
    );
}

// Modern Line Chart Component
function ModernLineChart({ data }: { data: { label: string; value: number }[] }) {
    const maxValue = Math.max(...data.map(d => d.value));
    const points = data.map((item, idx) => {
        const x = (idx / (data.length - 1)) * 100;
        const y = 100 - (item.value / maxValue) * 80;
        return `${x},${y}`;
    }).join(' ');

    return (
        <div className="relative">
            <div className="absolute left-0 right-0 top-0 flex justify-between text-xs text-gray-400">
                <span>0</span>
                <span>{Math.round(maxValue / 2)}</span>
                <span>{maxValue}</span>
            </div>
            <div className="mt-6 h-48 w-full">
                <svg className="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="lineGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stopColor="#3b82f6" stopOpacity="0.3" />
                            <stop offset="100%" stopColor="#8b5cf6" stopOpacity="0.3" />
                        </linearGradient>
                    </defs>
                    <polyline
                        points={points}
                        fill="none"
                        stroke="url(#lineGradient)"
                        strokeWidth="2"
                        className="stroke-blue-500 dark:stroke-blue-400"
                    />
                    <polygon
                        points={`0,100 ${points} 100,100`}
                        fill="url(#lineGradient)"
                        opacity="0.2"
                    />
                </svg>
            </div>
            <div className="mt-2 flex justify-between">
                {data.map((item, idx) => (
                    <div key={idx} className="text-center">
                        <div className="text-xs font-medium text-gray-600 dark:text-gray-400">{item.label}</div>
                    </div>
                ))}
            </div>
        </div>
    );
}

// Chat Button Component
function ChatButton({ onClick }: { onClick: () => void }) {
    return (
        <button
            onClick={onClick}
            className="group fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg transition-all duration-300 hover:scale-110 hover:shadow-xl active:scale-95 md:h-16 md:w-16"
        >
            <div className="absolute inset-0 rounded-full bg-white opacity-0 transition-opacity duration-300 group-hover:opacity-20" />
            <HiOutlineChatAlt2 className="h-6 w-6 md:h-7 md:w-7" />
            <span className="absolute -right-1 -top-1 h-4 w-4 rounded-full bg-green-500 ring-2 ring-white dark:ring-gray-900" />
        </button>
    );
}

// Chat Popup Component
function ChatPopup({ onClose }: { onClose: () => void }) {
    const [messages, setMessages] = useState<Message[]>([
        { id: 1, role: 'assistant', content: 'Halo! Saya asisten buku Anda. 👋\n\nAda yang bisa saya bantu? Cari buku, cek harga, atau rekomendasi sesuai budget? 😊' },
    ]);
    const [inputMessage, setInputMessage] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [conversationId, setConversationId] = useState<string | null>(null);
    const messagesEndRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLTextAreaElement>(null);
    const popupRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    useEffect(() => {
        inputRef.current?.focus();
    }, []);

    // Close popup when clicking outside (for desktop)
    useEffect(() => {
        function handleClickOutside(event: MouseEvent) {
            if (popupRef.current && !popupRef.current.contains(event.target as Node)) {
                // Don't close if clicking on chat button
                const chatButton = document.querySelector('.chat-button');
                if (chatButton && !chatButton.contains(event.target as Node)) {
                    onClose();
                }
            }
        }

        if (window.innerWidth >= 768) { // Only for desktop
            document.addEventListener('mousedown', handleClickOutside);
            return () => document.removeEventListener('mousedown', handleClickOutside);
        }
    }, [onClose]);

    // Handle ESC key to close
    useEffect(() => {
        const handleEscKey = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                onClose();
            }
        };

        document.addEventListener('keydown', handleEscKey);
        return () => document.removeEventListener('keydown', handleEscKey);
    }, [onClose]);

    const sendMessage = async () => {
        if (!inputMessage.trim() || isLoading) return;
        const userMessage: Message = { id: Date.now(), role: 'user', content: inputMessage };
        setMessages(prev => [...prev, userMessage]);
        setInputMessage('');
        setIsLoading(true);
        try {
            const response = await axios.post('/ai/chat', { query: inputMessage, conversation_id: conversationId });
            const assistantMessage: Message = { id: Date.now() + 1, role: 'assistant', content: response.data.response, books: response.data.books };
            setMessages(prev => [...prev, assistantMessage]);
            if (response.data.conversation_id) setConversationId(response.data.conversation_id);
        } catch (error) {
            setMessages(prev => [...prev, { id: Date.now() + 1, role: 'assistant', content: 'Maaf, terjadi kesalahan. Silakan coba lagi. 😔' }]);
        } finally {
            setIsLoading(false);
        }
    };

    const formatPrice = (price: number) => new Intl.NumberFormat('id-ID').format(price);
    const quickQuestions = [
        { label: '📚 Daftar buku', query: 'Tampilkan semua buku', icon: <FiBook className="h-3 w-3" /> },
        { label: '💰 Budget 200rb', query: 'Rekomendasi buku budget 200rb', icon: <FiDollarSign className="h-3 w-3" /> },
        { label: '💻 Programming', query: 'Cari buku programming', icon: <FiPackage className="h-3 w-3" /> },
        { label: '📖 Cek harga', query: 'Atomic Habits harganya berapa?', icon: <FiBook className="h-3 w-3" /> },
    ];

    return (
        <>
            {/* Overlay untuk mobile - klik di luar untuk close */}
            <div
                className="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm transition-all duration-300 md:hidden"
                onClick={onClose}
            />

            {/* Chat Popup */}
            <div
                ref={popupRef}
                className={`
                    fixed z-50 flex flex-col overflow-hidden bg-white shadow-2xl transition-all duration-300 dark:bg-gray-900
                    // Mobile styles (bottom sheet)
                    bottom-0 left-0 right-0 h-[85vh] w-full rounded-t-3xl
                    // Desktop styles (floating)
                    md:bottom-24 md:right-6 md:left-auto md:h-[600px] md:w-[420px] md:rounded-2xl md:shadow-xl
                `}
            >
                {/* Header dengan drag handle untuk mobile */}
                <div className="flex items-center justify-between bg-gradient-to-r from-blue-600 to-blue-500 px-5 py-4">
                    <div className="flex items-center gap-3">
                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-white/20 backdrop-blur">
                            <FaRobot className="h-5 w-5 text-white" />
                        </div>
                        <div>
                            <h3 className="font-semibold text-white">Book Assistant</h3>
                            <p className="text-xs text-blue-100">Online • Siap membantu</p>
                        </div>
                    </div>
                    <div className="flex items-center gap-2">
                        {/* Info untuk desktop */}
                        <span className="hidden text-xs text-white/60 md:inline">Tekan ESC untuk tutup</span>
                        <button
                            onClick={onClose}
                            className="rounded-full p-2 text-white transition-colors hover:bg-white/20"
                            aria-label="Close chat"
                        >
                            <FiX className="h-5 w-5" />
                        </button>
                    </div>
                </div>

                {/* Drag handle untuk mobile (indicator) */}
                <div className="block w-full pt-2 md:hidden">
                    <div className="mx-auto h-1 w-12 rounded-full bg-gray-300 dark:bg-gray-600" />
                </div>

                {/* Messages Area - Scrollable */}
                <div className="flex-1 overflow-y-auto p-4 space-y-4 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800/50 dark:to-gray-900">
                    {messages.map((message) => (
                        <div key={message.id} className={`flex ${message.role === 'user' ? 'justify-end' : 'justify-start'}`}>
                            <div className={`max-w-[85%] rounded-2xl px-4 py-2.5 ${message.role === 'user' ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-br-none' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-bl-none shadow-md'}`}>
                                <div className="whitespace-pre-wrap text-sm">{message.content}</div>
                                {message.books && message.books.length > 0 && (
                                    <div className="mt-3 space-y-2">
                                        {message.books.slice(0, 2).map((book) => (
                                            <div key={book.id} className="rounded-xl bg-white dark:bg-gray-900 p-3 text-xs border border-gray-100 dark:border-gray-700 shadow-sm">
                                                <div className="font-semibold text-sm">{book.title}</div>
                                                <div className="text-gray-500 text-xs mt-0.5">{book.author}</div>
                                                <div className="mt-2 flex items-center justify-between">
                                                    <span className="font-bold text-blue-600">Rp {formatPrice(book.price)}</span>
                                                    <span className={book.stock > 0 ? 'text-green-600' : 'text-red-600'}>
                                                        {book.stock > 0 ? `✅ Stok ${book.stock}` : '❌ Habis'}
                                                    </span>
                                                </div>
                                            </div>
                                        ))}
                                        {message.books.length > 2 && <div className="text-center text-xs text-blue-500">+{message.books.length - 2} lainnya</div>}
                                    </div>
                                )}
                            </div>
                        </div>
                    ))}
                    {isLoading && (
                        <div className="flex justify-start">
                            <div className="rounded-2xl rounded-bl-none bg-white dark:bg-gray-800 px-4 py-2.5 shadow-md">
                                <div className="flex gap-1.5">
                                    <span className="h-2 w-2 animate-bounce rounded-full bg-gray-400" />
                                    <span className="h-2 w-2 animate-bounce rounded-full bg-gray-400" style={{ animationDelay: '0.1s' }} />
                                    <span className="h-2 w-2 animate-bounce rounded-full bg-gray-400" style={{ animationDelay: '0.2s' }} />
                                </div>
                            </div>
                        </div>
                    )}
                    <div ref={messagesEndRef} />
                </div>

                {/* Quick Questions */}
                <div className="border-t border-gray-100 dark:border-gray-800 p-3 bg-gray-50 dark:bg-gray-800/50">
                    <div className="flex flex-wrap gap-2">
                        {quickQuestions.map((q, idx) => (
                            <button
                                key={idx}
                                onClick={() => { setInputMessage(q.query); sendMessage(); }}
                                className="flex items-center gap-1 rounded-full bg-gray-200 dark:bg-gray-700 px-3 py-1.5 text-xs font-medium transition-colors hover:bg-gray-300 dark:hover:bg-gray-600"
                                disabled={isLoading}
                            >
                                {q.icon}
                                {q.label}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Input Area */}
                <div className="border-t border-gray-100 dark:border-gray-800 p-4 bg-white dark:bg-gray-900">
                    <div className="flex gap-2">
                        <textarea
                            ref={inputRef}
                            value={inputMessage}
                            onChange={(e) => setInputMessage(e.target.value)}
                            onKeyPress={(e) => e.key === 'Enter' && !e.shiftKey && sendMessage()}
                            placeholder="Ketik pesan..."
                            className="flex-1 resize-none rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            rows={1}
                            disabled={isLoading}
                            style={{ maxHeight: '80px' }}
                        />
                        <button
                            onClick={sendMessage}
                            disabled={isLoading || !inputMessage.trim()}
                            className="rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-5 text-white transition-all hover:scale-105 disabled:opacity-50"
                        >
                            <FiSend className="h-5 w-5" />
                        </button>
                    </div>
                    <div className="mt-2 text-center text-[10px] text-gray-400">
                        <span className="hidden md:inline">💡 Tekan Enter untuk kirim, Shift+Enter untuk baris baru</span>
                        <span className="md:hidden">💡 Ketik pesan, Enter untuk kirim</span>
                    </div>
                </div>
            </div>
        </>
    );
}

// Main Dashboard Component
export default function Dashboard() {
    const [isChatOpen, setIsChatOpen] = useState(false);
    const [stats, setStats] = useState<DashboardStats>({
        totalBooks: 0, totalUsers: 0, totalCategories: 0, totalStock: 0, totalValue: 0, lowStock: 0,
        monthlySales: [], categoryDistribution: []
    });
    const [isLoading, setIsLoading] = useState(true);

    const categoryData = [
        { label: 'Teknologi', value: 45, color: 'bg-gradient-to-r from-blue-500 to-blue-400' },
        { label: 'Fiksi', value: 38, color: 'bg-gradient-to-r from-emerald-500 to-emerald-400' },
        { label: 'Self Development', value: 32, color: 'bg-gradient-to-r from-amber-500 to-amber-400' },
        { label: 'Bisnis', value: 28, color: 'bg-gradient-to-r from-purple-500 to-purple-400' },
        { label: 'Lainnya', value: 57, color: 'bg-gradient-to-r from-gray-500 to-gray-400' },
    ];

    const monthlyData = [
        { label: 'Jan', value: 12 }, { label: 'Feb', value: 19 }, { label: 'Mar', value: 15 },
        { label: 'Apr', value: 27 }, { label: 'Mei', value: 35 }, { label: 'Jun', value: 42 },
    ];

    useEffect(() => { fetchDashboardStats(); }, []);

    const fetchDashboardStats = async () => {
        try {
            const response = await axios.get('/api/dashboard/stats');
            setStats(response.data);
        } catch (error) {
            setStats({ totalBooks: 128, totalUsers: 1542, totalCategories: 12, totalStock: 3450, totalValue: 89750000, lowStock: 8, monthlySales: [], categoryDistribution: [] });
        } finally { setIsLoading(false); }
    };

    if (isLoading) {
        return (
            <AppLayout breadcrumbs={breadcrumbs}>
                <Head title="Dashboard" />
                <div className="flex h-screen items-center justify-center">
                    <div className="text-center">
                        <div className="mx-auto h-16 w-16 animate-spin rounded-full border-4 border-blue-500 border-t-transparent" />
                        <p className="mt-4 text-gray-500">Memuat dashboard...</p>
                    </div>
                </div>
            </AppLayout>
        );
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard | BookShop" />

            <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 p-4 md:p-6">

                {/* Hero Section */}
                <div className="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 via-blue-500 to-purple-600 p-8 text-white shadow-xl">
                    <div className="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl" />
                    <div className="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-purple-500/20 blur-3xl" />
                    <div className="relative z-10">
                        <div className="flex items-center gap-3">
                            <MdOutlineDashboard className="h-8 w-8" />
                            <h1 className="text-3xl font-bold md:text-4xl">BookShop Dashboard</h1>
                        </div>
                        <p className="mt-2 text-blue-100">Kelola toko buku digital Anda dengan mudah dan profesional</p>
                        <div className="mt-4 flex flex-wrap gap-3">
                            <span className="flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 text-sm backdrop-blur">
                                <FaBook className="h-3 w-3" /> 100+ Buku
                            </span>
                            <span className="flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 text-sm backdrop-blur">
                                <HiOutlineUsers className="h-3 w-3" /> 1000+ User
                            </span>
                            <span className="flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 text-sm backdrop-blur">
                                <FiStar className="h-3 w-3" /> 4.8 Rating
                            </span>
                        </div>
                    </div>
                </div>

                {/* Stats Grid */}
                <div className="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard title="Total Buku" value={stats.totalBooks} icon={MdOutlineLibraryBooks} trend="up" trendValue="+12%" color="from-blue-500 to-blue-400" />
                    <StatCard title="Total User" value={stats.totalUsers} icon={HiOutlineUsers} trend="up" trendValue="+8%" color="from-emerald-500 to-emerald-400" />
                    <StatCard title="Total Kategori" value={stats.totalCategories} icon={MdOutlineCategory} color="from-amber-500 to-amber-400" />
                    <StatCard title="Total Stok" value={stats.totalStock} icon={MdOutlineInventory} trend="down" trendValue="-3%" color="from-purple-500 to-purple-400" />
                </div>

                {/* Second Row Stats */}
                <div className="mb-8 grid grid-cols-1 gap-5 md:grid-cols-2">
                    <StatCard title="Nilai Inventori" value={`Rp ${stats.totalValue.toLocaleString()}`} icon={MdOutlineAttachMoney} color="from-rose-500 to-rose-400" />
                    <StatCard title="Stok Menipis" value={stats.lowStock} icon={MdOutlineWarning} color="from-orange-500 to-orange-400" />
                </div>

                {/* Charts Section */}
                <div className="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div className="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-900">
                        <div className="mb-5 flex items-center justify-between">
                            <div className="flex items-center gap-2">
                                <FiBarChart2 className="h-5 w-5 text-blue-500" />
                                <h3 className="text-lg font-semibold text-gray-900 dark:text-white">Distribusi Kategori</h3>
                            </div>
                            <span className="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">Per 2024</span>
                        </div>
                        <ModernBarChart data={categoryData} />
                    </div>
                    <div className="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-900">
                        <div className="mb-5 flex items-center justify-between">
                            <div className="flex items-center gap-2">
                                <BsGraphUp className="h-5 w-5 text-emerald-500" />
                                <h3 className="text-lg font-semibold text-gray-900 dark:text-white">Tren Penjualan</h3>
                            </div>
                            <span className="flex items-center gap-1 text-xs text-green-600">
                                <FiTrendingUp className="h-3 w-3" /> 23% dari bulan lalu
                            </span>
                        </div>
                        <ModernLineChart data={monthlyData} />
                    </div>
                </div>

                {/* Recent Activity & Quick Actions */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div className="lg:col-span-2 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-900">
                        <div className="mb-5 flex items-center gap-2">
                            <FiActivity className="h-5 w-5 text-purple-500" />
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">Aktivitas Terbaru</h3>
                        </div>
                        <div className="space-y-4">
                            <div className="flex items-center gap-4 rounded-xl p-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                    <FiBook className="h-5 w-5" />
                                </div>
                                <div className="flex-1">
                                    <p className="text-sm font-medium text-gray-900 dark:text-white">Buku baru ditambahkan</p>
                                    <p className="text-xs text-gray-500">Atomic Habits oleh James Clear</p>
                                </div>
                                <span className="flex items-center gap-1 text-xs text-gray-400">
                                    <FiClock className="h-3 w-3" /> 2 menit lalu
                                </span>
                            </div>
                            <div className="flex items-center gap-4 rounded-xl p-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <HiOutlineUsers className="h-5 w-5" />
                                </div>
                                <div className="flex-1">
                                    <p className="text-sm font-medium text-gray-900 dark:text-white">User baru mendaftar</p>
                                    <p className="text-xs text-gray-500">John Doe - john@example.com</p>
                                </div>
                                <span className="flex items-center gap-1 text-xs text-gray-400">
                                    <FiClock className="h-3 w-3" /> 15 menit lalu
                                </span>
                            </div>
                            <div className="flex items-center gap-4 rounded-xl p-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                                    <FiDollarSign className="h-5 w-5" />
                                </div>
                                <div className="flex-1">
                                    <p className="text-sm font-medium text-gray-900 dark:text-white">Transaksi berhasil</p>
                                    <p className="text-xs text-gray-500">Pembelian buku "Clean Code" - Rp 250.000</p>
                                </div>
                                <span className="flex items-center gap-1 text-xs text-gray-400">
                                    <FiClock className="h-3 w-3" /> 1 jam lalu
                                </span>
                            </div>
                            <div className="flex items-center gap-4 rounded-xl p-3 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800">
                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                                    <MdOutlineInventory className="h-5 w-5" />
                                </div>
                                <div className="flex-1">
                                    <p className="text-sm font-medium text-gray-900 dark:text-white">Stok diperbarui</p>
                                    <p className="text-xs text-gray-500">5 buku diperbarui stoknya</p>
                                </div>
                                <span className="flex items-center gap-1 text-xs text-gray-400">
                                    <FiClock className="h-3 w-3" /> 3 jam lalu
                                </span>
                            </div>
                        </div>
                    </div>
                    <div className="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-900">
                        <div className="mb-5 flex items-center gap-2">
                            <FaRocket className="h-5 w-5 text-blue-500" />
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">Aksi Cepat</h3>
                        </div>
                        <div className="space-y-3">
                            <button className="flex w-full items-center gap-3 rounded-xl bg-gradient-to-r from-blue-50 to-blue-100 p-3 text-left transition-all hover:scale-[1.02] dark:from-blue-950/30 dark:to-blue-900/30">
                                <div className="rounded-full bg-blue-500 p-2 text-white">
                                    <BsPlusCircle className="h-4 w-4" />
                                </div>
                                <div>
                                    <p className="font-medium text-gray-900 dark:text-white">Tambah Buku</p>
                                    <p className="text-xs text-gray-500">Input buku baru ke sistem</p>
                                </div>
                            </button>
                            <button className="flex w-full items-center gap-3 rounded-xl bg-gradient-to-r from-emerald-50 to-emerald-100 p-3 text-left transition-all hover:scale-[1.02] dark:from-emerald-950/30 dark:to-emerald-900/30">
                                <div className="rounded-full bg-emerald-500 p-2 text-white">
                                    <FaUserPlus className="h-4 w-4" />
                                </div>
                                <div>
                                    <p className="font-medium text-gray-900 dark:text-white">Tambah User</p>
                                    <p className="text-xs text-gray-500">Registrasi user baru</p>
                                </div>
                            </button>
                            <button className="flex w-full items-center gap-3 rounded-xl bg-gradient-to-r from-amber-50 to-amber-100 p-3 text-left transition-all hover:scale-[1.02] dark:from-amber-950/30 dark:to-amber-900/30">
                                <div className="rounded-full bg-amber-500 p-2 text-white">
                                    <FiGrid className="h-4 w-4" />
                                </div>
                                <div>
                                    <p className="font-medium text-gray-900 dark:text-white">Kelola Kategori</p>
                                    <p className="text-xs text-gray-500">Atur kategori buku</p>
                                </div>
                            </button>
                            <button className="flex w-full items-center gap-3 rounded-xl bg-gradient-to-r from-purple-50 to-purple-100 p-3 text-left transition-all hover:scale-[1.02] dark:from-purple-950/30 dark:to-purple-900/30">
                                <div className="rounded-full bg-purple-500 p-2 text-white">
                                    <FaChartLine className="h-4 w-4" />
                                </div>
                                <div>
                                    <p className="font-medium text-gray-900 dark:text-white">Laporan</p>
                                    <p className="text-xs text-gray-500">Lihat laporan lengkap</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <ChatButton onClick={() => setIsChatOpen(true)} />
            {isChatOpen && <ChatPopup onClose={() => setIsChatOpen(false)} />}
        </AppLayout>
    );
}
