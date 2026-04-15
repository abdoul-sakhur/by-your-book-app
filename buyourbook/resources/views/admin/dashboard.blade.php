<x-admin-layout>
    <x-slot name="header">Tableau de bord</x-slot>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
        <a href="{{ route('admin.schools.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition">
            <div class="text-sm font-medium text-gray-500">Écoles</div>
            <div class="mt-2 text-3xl font-bold" style="color: var(--color-primary);">{{ $schoolsCount }}</div>
        </a>

        <a href="{{ route('admin.official-books.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition">
            <div class="text-sm font-medium text-gray-500">Livres officiels</div>
            <div class="mt-2 text-3xl font-bold" style="color: var(--color-primary);">{{ $booksCount }}</div>
        </a>

        <a href="{{ route('admin.seller-books.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition">
            <div class="text-sm font-medium text-gray-500">Livres en attente</div>
            <div class="mt-2 text-3xl font-bold" style="color: var(--color-secondary);">{{ $pendingCount }}</div>
        </a>

        <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition">
            <div class="text-sm font-medium text-gray-500">Commandes</div>
            <div class="mt-2 text-3xl font-bold" style="color: var(--color-accent);">{{ $ordersCount }}</div>
        </a>

        <a href="{{ route('admin.users.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition">
            <div class="text-sm font-medium text-gray-500">Utilisateurs</div>
            <div class="mt-2 text-3xl font-bold text-gray-700">{{ $usersCount }}</div>
        </a>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-500">Chiffre d'affaires</div>
            <div class="mt-2 text-2xl font-bold" style="color: var(--color-primary);">{{ number_format($revenue, 0, ',', ' ') }} F</div>
        </div>
    </div>

    <!-- Recent Orders + Pending Books -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Graphique CA 6 mois -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">📊 Chiffre d'affaires (6 derniers mois)</h2>
            <canvas id="revenueChart" height="200"></canvas>
        </div>

        <!-- Graphique Commandes par statut -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">📦 Commandes par statut</h2>
            <canvas id="ordersChart" height="200"></canvas>
        </div>

        <!-- Dernières commandes -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Dernières commandes</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium hover:underline" style="color: var(--color-primary);">Tout voir</a>
            </div>
            <div class="divide-y">
                @forelse ($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition">
                        <div>
                            <span class="font-medium text-gray-800">#{{ $order->id }}</span>
                            <span class="text-gray-500 ml-2">{{ $order->user->name ?? '—' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-gray-700">{{ number_format($order->total_amount, 0, ',', ' ') }} F</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-800">
                                {{ $order->status->label() }}
                            </span>
                        </div>
                    </a>
                @empty
                    <p class="px-6 py-4 text-gray-500 text-sm">Aucune commande pour le moment.</p>
                @endforelse
            </div>
        </div>

        <!-- Livres en attente de validation -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Livres en attente</h2>
                <a href="{{ route('admin.seller-books.index') }}" class="text-sm font-medium hover:underline" style="color: var(--color-primary);">Tout voir</a>
            </div>
            <div class="divide-y">
                @forelse ($pendingBooks as $book)
                    <a href="{{ route('admin.seller-books.show', $book) }}" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition">
                        <div class="min-w-0 flex-1">
                            <div class="font-medium text-gray-800 truncate">{{ $book->officialBook->title ?? '—' }}</div>
                            <div class="text-sm text-gray-500">par {{ $book->seller->name ?? '—' }}</div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 ml-3">
                            En attente
                        </span>
                    </a>
                @empty
                    <p class="px-6 py-4 text-gray-500 text-sm">Aucun livre en attente.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top vendeurs -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">🏆 Top vendeurs</h2>
        </div>
        <div class="divide-y">
            @forelse ($topSellers as $index => $seller)
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white {{ $index === 0 ? 'bg-yellow-500' : ($index === 1 ? 'bg-gray-400' : 'bg-amber-700') }}">
                            {{ $index + 1 }}
                        </span>
                        <span class="font-medium text-gray-800">{{ $seller->name }}</span>
                    </div>
                    <span class="text-sm text-gray-500">{{ $seller->sold_count }} livre(s) vendu(s)</span>
                </div>
            @empty
                <p class="px-6 py-4 text-gray-500 text-sm">Aucun vendeur pour le moment.</p>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        // Revenue chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(collect($revenueData)->pluck('month')) !!},
                datasets: [{
                    label: 'CA (F CFA)',
                    data: {!! json_encode(collect($revenueData)->pluck('total')) !!},
                    backgroundColor: 'rgba(27, 77, 62, 0.7)',
                    borderColor: '#1B4D3E',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => v.toLocaleString('fr-FR') + ' F' }
                    }
                }
            }
        });

        // Orders by status chart
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        const statusLabels = {
            'pending': 'En attente',
            'confirmed': 'Confirmée',
            'preparing': 'En préparation',
            'ready': 'Prête',
            'delivered': 'Livrée',
            'cancelled': 'Annulée'
        };
        const statusColors = {
            'pending': '#F59E0B',
            'confirmed': '#3B82F6',
            'preparing': '#8B5CF6',
            'ready': '#10B981',
            'delivered': '#1B4D3E',
            'cancelled': '#EF4444'
        };
        const orderData = @json($ordersByStatus);
        new Chart(ordersCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(orderData).map(k => statusLabels[k] || k),
                datasets: [{
                    data: Object.values(orderData),
                    backgroundColor: Object.keys(orderData).map(k => statusColors[k] || '#9CA3AF'),
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 15 } }
                }
            }
        });
    </script>
    @endpush
</x-admin-layout>
