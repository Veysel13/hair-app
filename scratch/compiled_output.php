<?php $__env->startSection('title', $business->name . ' — Saç & Güzellik Randevusu'); ?>

<?php $__env->startPush('head'); ?>
<meta name="description" content="<?php echo e(Str::limit($business->description ?? $business->name . ' salonumuzda saç tasarımı, kesim, boya ve bakım hizmetlerimizden randevu alın.', 150)); ?>">
<meta property="og:title" content="<?php echo e($business->name); ?> — HairApp">
<meta property="og:description" content="<?php echo e(Str::limit($business->description, 150)); ?>">
<?php if($business->cover_image): ?>
<meta property="og:image" content="<?php echo e(asset($business->cover_image)); ?>">
<?php endif; ?>
<meta property="og:type" content="business.business">


<script type="application/ld+json">
{
  "<?php echo e('@'); ?>context": "https://schema.org",
  "<?php echo e('@'); ?>type": "BeautySalon",
  "name": "<?php echo e($business->name); ?>",
  "image": "<?php echo e($business->logo ? asset($business->logo) : asset('default-logo.png')); ?>",
  "<?php echo e('@'); ?>id": "<?php echo e(url()->current()); ?>",
  "url": "<?php echo e(url()->current()); ?>",
  "telephone": "<?php echo e($business->phone); ?>",
  "address": {
    "<?php echo e('@'); ?>type": "PostalAddress",
    "streetAddress": "<?php echo e($business->address); ?>",
    "addressLocality": "<?php echo e($business->district); ?>",
    "addressRegion": "<?php echo e($business->city); ?>",
    "addressCountry": "TR"
  },
  "geo": {
    "<?php echo e('@'); ?>type": "GeoCoordinates",
    "latitude": "<?php echo e($business->latitude ?? '41.0082'); ?>",
    "longitude": "<?php echo e($business->longitude ?? '28.9784'); ?>"
  },
  "openingHoursSpecification": [
    <?php $__currentLoopData = $business->hours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    {
      "<?php echo e('@'); ?>type": "OpeningHoursSpecification",
      "dayOfWeek": "<?php echo e(match($h->day_of_week) { 1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 0=>'Sunday' }); ?>",
      "opens": "<?php echo e($h->is_open ? substr($h->open_time, 0, 5) : '00:00'); ?>",
      "closes": "<?php echo e($h->is_open ? substr($h->close_time, 0, 5) : '00:00'); ?>"
    }<?php echo e(!$loop->last ? ',' : ''); ?>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  ]
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">

    
    <div class="relative rounded-3xl overflow-hidden border border-white/5 bg-[#121218] shadow-2xl">
        
        <div class="h-64 sm:h-80 md:h-96 w-full relative">
            <?php if($business->cover_image): ?>
            <img src="<?php echo e(asset($business->cover_image)); ?>" alt="<?php echo e($business->name); ?>" class="w-full h-full object-cover">
            <?php else: ?>
            <div class="w-full h-full gradient-brand opacity-20"></div>
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-[#0f0f14] via-[#0f0f14]/40 to-transparent"></div>
        </div>

        
        <div class="absolute bottom-0 inset-x-0 p-6 sm:p-8 flex flex-col sm:flex-row sm:items-end justify-between gap-6">
            <div class="flex items-center gap-4 sm:gap-6">
                
                <div class="w-20 h-20 sm:w-28 sm:h-28 rounded-2xl overflow-hidden bg-[#121218] border-2 border-purple-500/30 shadow-xl flex-shrink-0">
                    <?php if($business->logo): ?>
                    <img src="<?php echo e(asset($business->logo)); ?>" alt="<?php echo e($business->name); ?> Logo" class="w-full h-full object-cover">
                    <?php else: ?>
                    <div class="w-full h-full gradient-brand flex items-center justify-center text-white text-3xl font-black">
                        <?php echo e(substr($business->name, 0, 1)); ?>

                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl sm:text-3xl font-extrabold text-white tracking-tight"><?php echo e($business->name); ?></h1>
                        <span class="badge badge-purple text-[10px] sm:text-xs">
                            <?php echo e(match($business->category) {
                                'hair_salon' => 'Kuaför',
                                'barber' => 'Berber',
                                'nail_salon' => 'Tırnak Salonu',
                                'spa' => 'Spa',
                                'beauty_center' => 'Güzellik Merkezi',
                                default => 'Salon'
                            }); ?>

                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-[#a0a0c0] flex items-center gap-1">
                        📍 <span><?php echo e($business->address); ?>, <?php echo e($business->district); ?> / <?php echo e($business->city); ?></span>
                    </p>
                    <div class="flex items-center gap-3 text-xs sm:text-sm">
                        <div class="flex items-center gap-1 text-yellow-400 font-bold">
                            ★ <span><?php echo e(number_format($business->avg_rating ?? 5.0, 1)); ?></span>
                            <span class="text-[#7070a0] font-normal">(<?php echo e($business->total_reviews ?? 0); ?> yorum)</span>
                        </div>
                        <span class="text-white/10">|</span>
                        <?php if($business->isOpen()): ?>
                        <span class="text-green-400 font-bold flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span> Şimdi Açık
                        </span>
                        <?php else: ?>
                        <span class="text-red-400 font-bold flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-red-400"></span> Kapalı
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="flex-shrink-0">
                <a href="<?php echo e(route('book.show', $business)); ?>" class="btn btn-primary btn-lg px-8 py-3.5 text-sm font-bold shadow-lg shadow-purple-500/20 w-full sm:w-auto text-center flex items-center justify-center gap-2 transition-all hover:scale-105">
                    Randevu Al ⚡
                </a>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        
        <div class="lg:col-span-2 space-y-8">
            
            
            <?php if($business->description): ?>
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>✨</span> Hakkımızda
                </h3>
                <p class="text-sm text-[#a0a0c0] leading-relaxed"><?php echo e($business->description); ?></p>
            </div>
            <?php endif; ?>

            
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>💇‍♀️</span> Hizmet Menümüz
                </h3>

                <?php $groupedServices = $business->activeServices->groupBy(fn($s) => $s->category?->name ?? 'Genel') ?>

                <?php $__empty_1 = true; $__currentLoopData = $groupedServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catName => $catServices): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-[#7070a0] uppercase tracking-wider"><?php echo e($catName); ?></h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <?php $__currentLoopData = $catServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="p-4 rounded-2xl bg-white/3 border border-white/5 flex items-center justify-between gap-3 hover:border-purple-500/20 transition-all">
                            <div>
                                <h5 class="text-xs font-bold text-white"><?php echo e($s->name); ?></h5>
                                <p class="text-[10px] text-[#7070a0] mt-0.5">⏱️ <?php echo e($s->duration_minutes); ?> dk</p>
                            </div>
                            <span class="text-xs font-bold text-[#e879f9]"><?php echo e(number_format($s->price, 0, ',', '.')); ?> ₺</span>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-12 text-[#7070a0] text-sm">Hizmet bilgisi bulunamadı.</div>
                <?php endif; ?>
            </div>

            
            <?php if($business->images->isNotEmpty()): ?>
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>🖼️</span> Galeri & Çalışmalarımız
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <?php $__currentLoopData = $business->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="group relative aspect-square rounded-2xl overflow-hidden border border-white/5 bg-[#121218]">
                        <img src="<?php echo e(asset($img->image_path)); ?>" alt="<?php echo e($img->caption ?? 'Gallery Image'); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <?php if($img->caption): ?>
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-3 pt-6 opacity-0 group-hover:opacity-100 transition-opacity">
                            <p class="text-[10px] text-white font-medium truncate"><?php echo e($img->caption); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

            
            <?php if($business->activeStaff->isNotEmpty()): ?>
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>🤝</span> Uzman Kadromuz
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <?php $__currentLoopData = $business->activeStaff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-4 rounded-2xl bg-white/3 border border-white/5 flex flex-col items-center text-center space-y-3">
                        <div class="w-16 h-16 rounded-full overflow-hidden bg-[#121218] border-2 border-purple-500/10 flex items-center justify-center text-white text-xl font-bold">
                            <?php echo e(substr($member->name, 0, 1)); ?>

                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white"><?php echo e($member->name); ?></h4>
                            <p class="text-[9px] text-[#7070a0] uppercase font-semibold mt-0.5"><?php echo e($member->title ?? 'Uzman'); ?></p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>⭐</span> Müşteri Yorumları
                </h3>

                <?php $__empty_1 = true; $__currentLoopData = $business->visibleReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-4 rounded-2xl bg-white/3 border border-white/5 space-y-3">
                    <div class="flex justify-between items-center gap-3">
                        <div>
                            <h5 class="text-xs font-bold text-white"><?php echo e($review->customer->name); ?></h5>
                            <p class="text-[9px] text-[#7070a0]"><?php echo e($review->created_at->format('d M Y')); ?></p>
                        </div>
                        <div class="flex items-center gap-1 text-yellow-400 font-bold text-xs bg-yellow-400/5 px-2 py-1 rounded-lg border border-yellow-400/10">
                            ★ <?php echo e($review->rating); ?>

                        </div>
                    </div>
                    
                    <?php if($review->comment): ?>
                    <p class="text-xs text-[#a0a0c0] leading-relaxed italic">"<?php echo e($review->comment); ?>"</p>
                    <?php endif; ?>

                    <?php if($review->business_reply): ?>
                    <div class="bg-purple-950/20 border border-purple-500/10 p-3 rounded-xl mt-2 space-y-1">
                        <p class="text-[10px] font-bold text-[#e879f9] flex items-center gap-1">
                            <span>💬</span> Salonun Yanıtı:
                        </p>
                        <p class="text-xs text-[#a0a0c0] leading-relaxed"><?php echo e($review->business_reply); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-12 text-[#7070a0] text-sm">
                    Henüz yorum yapılmamış. İlk yorum yapan siz olun!
                </div>
                <?php endif; ?>
            </div>

        </div>

        
        <div class="space-y-6">
            
            
            <div class="glass-card rounded-3xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>📆</span> Çalışma Saatleri
                </h3>
                <div class="space-y-2">
                    <?php $__currentLoopData = $business->hours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex justify-between items-center text-xs py-1.5 border-b border-white/3 last:border-0">
                        <span class="text-[#a0a0c0]">
                            <?php echo e(match($hour->day_of_week) {
                                1 => 'Pazartesi',
                                2 => 'Salı',
                                3 => 'Çarşamba',
                                4 => 'Perşembe',
                                5 => 'Cuma',
                                6 => 'Cumartesi',
                                0 => 'Pazar'
                            }); ?>

                        </span>
                        <?php if($hour->is_open): ?>
                        <span class="text-white font-semibold"><?php echo e(substr($hour->open_time, 0, 5)); ?> - <?php echo e(substr($hour->close_time, 0, 5)); ?></span>
                        <?php else: ?>
                        <span class="text-red-400 font-bold">Kapalı</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div class="glass-card rounded-3xl p-6 space-y-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-white/5 pb-3">
                    <span>📞</span> İletişim Bilgileri
                </h3>
                
                <div class="space-y-3.5 text-xs">
                    
                    <div class="flex items-start gap-3">
                        <span class="text-base">📱</span>
                        <div>
                            <p class="text-[#7070a0] text-[10px] uppercase font-bold">Telefon</p>
                            <a href="tel:<?php echo e($business->phone); ?>" class="text-white hover:text-purple-400 font-bold transition-colors"><?php echo e($business->phone); ?></a>
                        </div>
                    </div>

                    
                    <?php if($business->email): ?>
                    <div class="flex items-start gap-3">
                        <span class="text-base">✉️</span>
                        <div>
                            <p class="text-[#7070a0] text-[10px] uppercase font-bold">E-posta</p>
                            <a href="mailto:<?php echo e($business->email); ?>" class="text-white hover:text-purple-400 transition-colors"><?php echo e($business->email); ?></a>
                        </div>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($business->website): ?>
                    <div class="flex items-start gap-3">
                        <span class="text-base">🌐</span>
                        <div>
                            <p class="text-[#7070a0] text-[10px] uppercase font-bold">Web Sitesi</p>
                            <a href="<?php echo e($business->website); ?>" target="_blank" class="text-white hover:text-purple-400 transition-colors font-semibold truncate block max-w-[200px]"><?php echo e($business->website); ?></a>
                        </div>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($business->instagram): ?>
                    <div class="flex items-start gap-3">
                        <span class="text-base">📸</span>
                        <div>
                            <p class="text-[#7070a0] text-[10px] uppercase font-bold">Instagram</p>
                            <a href="https://instagram.com/<?php echo e($business->instagram); ?>" target="_blank" class="text-[#e879f9] hover:underline font-bold transition-all"><?php echo e('@' . $business->instagram); ?></a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="pt-2">
                    <a href="tel:<?php echo e($business->phone); ?>" class="w-full btn btn-secondary text-xs font-semibold py-2.5 flex items-center justify-center gap-1.5">
                        📞 Hemen Ara
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>