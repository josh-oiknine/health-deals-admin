# Price History Model Documentation

## Overview
The `PriceHistory` model tracks price changes for products in the Health Deals Admin system. It maintains a historical record of product prices over time, enabling price trend analysis and deal validation.

## Properties

| Property | Type | Description | Nullable |
|----------|------|-------------|-----------|
| `id` | int | Unique identifier | Yes |
| `product_id` | int | Associated product ID | No |
| `price` | float | Price at point in time | No |
| `created_at` | string | Record creation timestamp | Yes |

## Constructor

```php
public function __construct(
    int $product_id = 0,
    float $price = 0.0,
    ?string $created_at = null
)
```

Creates a new PriceHistory instance with the specified properties.

## Methods

### Static Methods

#### `findByProduct(int $product_id, int $limit = 99999999): array`
Retrieves price history records for a specific product.
- **Parameters**:
  - `$product_id` - Product ID
  - `$limit` - Maximum number of records to return
- **Returns**: Array of price history records
- **Order**: By creation date descending

#### `countByProduct(int $product_id): int`
Counts the number of price history records for a product.
- **Parameters**: `$product_id` - Product ID
- **Returns**: Number of price history records

### Getters and Setters

- `getId(): ?int`
- `setId(?int $id): void`
- `getProductId(): int`
- `setProductId(int $product_id): void`

## Database Schema

```sql
CREATE TABLE price_history (
    id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES products(id),
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Relationships

1. **Product**:
   - Many-to-One relationship
   - Each price history record belongs to one product
   - Foreign key: `product_id`

## Usage Examples

### Creating a Price History Record
```php
$priceHistory = new PriceHistory(
    123, // product_id
    29.99 // price
);
```

### Retrieving Price History
```php
// Get last 10 price records
$history = PriceHistory::findByProduct(123, 10);

// Count total price records
$count = PriceHistory::countByProduct(123);
```

## Error Handling

The model implements comprehensive error handling:
1. Database errors are logged using `error_log()`
2. Failed operations return empty arrays or zero
3. PDO exceptions are caught and handled gracefully
4. All database operations are wrapped in try-catch blocks

## Best Practices

1. **Price Recording**:
   - Record all price changes
   - Maintain chronological order
   - Use consistent precision
   - Validate price values

2. **Data Management**:
   - Regular cleanup of old records
   - Efficient storage strategies
   - Proper indexing
   - Data archiving

3. **Performance**:
   - Limit query results
   - Optimize frequent queries
   - Use prepared statements
   - Implement caching

## Implementation Details

### Price Storage
- Decimal precision (10,2)
- Non-negative values
- Currency handling
- Price formatting

### Record Management
- Timestamp tracking
- Record ordering
- Data retention
- Query optimization

### Data Analysis
- Price trend tracking
- Change detection
- Statistical analysis
- Report generation

## Use Cases

1. **Price Tracking**:
   - Monitor price changes
   - Track price trends
   - Identify price drops
   - Analyze pricing patterns

2. **Deal Validation**:
   - Verify deal savings
   - Compare historical prices
   - Validate discounts
   - Ensure deal accuracy

3. **Reporting**:
   - Price trend reports
   - Historical analysis
   - Price change alerts
   - Market insights

## Integration Points

1. **Product Management**:
   - Price update tracking
   - Product price history
   - Price change notifications
   - Historical data access

2. **Deal Management**:
   - Deal price verification
   - Savings calculation
   - Price trend analysis
   - Deal opportunity identification

3. **Analytics Integration**:
   - Price trend analysis
   - Market insights
   - Performance metrics
   - Data visualization

## Performance Considerations

1. **Query Optimization**:
   - Indexed lookups
   - Result limiting
   - Efficient sorting
   - Query caching

2. **Data Volume**:
   - Record retention policy
   - Data archiving strategy
   - Storage optimization
   - Query performance

3. **Access Patterns**:
   - Common query paths
   - Frequent operations
   - Data aggregation
   - Cache utilization 