<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            CREATE TRIGGER reserva_insert_check
            BEFORE INSERT ON reservas
            FOR EACH ROW
            BEGIN
                SELECT 
                    CASE
                        WHEN NEW.dinero_pendiente < 0
                        THEN RAISE(ABORT, "dinero_pendiente no puede ser < 0")
                    END;

                SELECT
                    CASE
                        WHEN NEW.dinero_pendiente > (
                            SELECT precio FROM pedidos WHERE id = NEW.pedido_id
                        )
                        THEN RAISE(ABORT, "dinero_pendiente no puede ser mayor que precio")
                    END;

            END;
        ');

        DB::unprepared('
            CREATE TRIGGER reserva_update_check
            BEFORE UPDATE ON reservas
            FOR EACH ROW
            BEGIN
                SELECT
                    CASE
                        WHEN NEW.dinero_pendiente > (
                            SELECT precio FROM pedidos WHERE id = NEW.pedido_id
                        )
                        THEN RAISE(ABORT, "dinero_pendiente no puede ser mayor que precio")
                    END;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER pedido_update_check 
            BEFORE UPDATE ON pedidos
            FOR EACH ROW
            BEGIN
                SELECT
                    CASE
                        WHEN NEW.precio < (
                            SELECT dinero_pendiente FROM reservas
                            WHERE pedido_id = NEW.id
                        )
                        THEN RAISE(ABORT, "El precio no puede ser inferior al dinero pendiente")
            
                    END;
            END;
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS reserva_insert_check;');
        DB::unprepared('DROP TRIGGER IF EXISTS reserva_update_check;');
        DB::unprepared('DROP TRIGGER IF EXISTS pedido_update_check;');
    }
};
