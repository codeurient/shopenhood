
const fs = require('fs');
const filePath = 'C:/laragon/www/shopenhood/app/Http/Controllers/User/ListingController.php';
let content = fs.readFileSync(filePath, 'utf8');

// CHANGE 1
const f1 = [
  '            // Store name, wholesale, SEO, availability, and variations only for business users',
  '            if (! ->isBusinessUser()) {',
  '                unset(['store_name']);',
  '                ['is_wholesale'] = false;',
  '                unset(['wholesale_min_order_qty']);',
  '                unset(['wholesale_qty_increment']);',
  '                unset(['wholesale_lead_time_days']);',
  '                unset(['wholesale_sample_available']);',
  '                unset(['wholesale_sample_price']);',
  '                unset(['wholesale_terms']);',
  '                unset(['meta_title']);',
  '                unset(['meta_description']);',
  '                // Availability, variants/variations are business-only features',
  '                unset(['availability_type']);',
  '                unset(['variants']);',
  '                unset(['variations']);',
  '            } else {',
  '                ['is_wholesale'] = ->has('is_wholesale');',
  '                ['wholesale_sample_available'] = ->has('wholesale_sample_available');',
  '            }',
].join('
');
console.log('f1 in content:', content.includes(f1));
