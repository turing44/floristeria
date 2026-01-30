<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            CREATE TRIGGER reserva_before_insert
            BEFORE INSERT ON reservas
            FOR EACH ROW
            BEGIN
                SELECT
                    CASE
                        WHEN NEW.dinero_pendiente > (
                            SELECT precio FROM pedidos WHERE id = NEW.pedido_id
                        )
                        THEN RAISE(ABORT, "dinero_pendiente no puede ser mayor que precio")
                    END;

                SELECT
                    CASE
                        WHEN NEW.estado_pago = "PAGADO" AND NEW.dinero_pendiente != 0
                        THEN RAISE(ABORT, "Si el pago está PAGADO, dinero_pendiente debe ser 0")
                    END;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER reserva_before_update
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

                SELECT
                    CASE
                        WHEN NEW.estado_pago = "PAGADO" AND NEW.dinero_pendiente != 0
                        THEN RAISE(ABORT, "Si el pago está PAGADO, dinero_pendiente debe ser 0")
                    END;
            END;
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS reserva_before_insert;');
        DB::unprepared('DROP TRIGGER IF EXISTS reserva_before_update;');
    }
};
